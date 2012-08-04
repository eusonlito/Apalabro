<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Apalabro {
    private $server = 'http://api.apalabrados.com/api/';
    private $logged = false;
    private $user = 0;
    private $session = '';
    private $languages = array();
    private $language = 'es';
    private $dictionary = array();
    private $dictionary_len = 0;
    private $words = array();
    private $games = array();
    private $my_rack_tiles = array();
    private $board_spaces = array();

    private $Curl;
    private $Debug;

    public $Cache;

    public function __construct ()
    {
        $this->Cache = new Cache;
        $this->Curl = new Curl;
        $this->Debug = new Debug;

        $this->Curl->init($this->server);

        $this->setLanguages();
        $this->setLanguage();
    }

    public function setDebug ($debug)
    {
        $this->Debug->setDebug($debug);
        $this->Curl->Debug->setDebug($debug);
    }

    public function debug ($text, $info = true)
    {
        $this->Debug->show($text, $info);
    }

    private function setCookie ($value) {
        $cookie = array_merge($this->getCookie(), (array)$value);

        setCookie('apalabro', gzdeflate(serialize($cookie)));
    }

    private function getCookie () {
        if (isset($_COOKIE['apalabro'])) {
            return unserialize(gzinflate($_COOKIE['apalabro']));
        } else {
            return array();
        }
    }

    public function setLanguages ($language = '', $store = false)
    {
        foreach (glob(BASE_PATH.'/languages/*', GLOB_ONLYDIR) as $language) {
            if (is_file($language.'/dictionary.txt') && is_file($language.'/words.txt')) {
                $this->languages[] = basename($language);
            }
        }
    }

    public function getLanguages ()
    {
        return $this->languages;
    }

    public function setLanguage ($language = '', $store = false)
    {
        $language = strtolower($language);

        if ($language === $this->language) {
            return true;
        }

        $cookie = $this->getCookie();

        if (!$language) {
            if (!isset($cookie['language']) || ($cookie['language'] === $this->language)) {
                return true;
            }

            $language = $cookie['language'];
        }

        if (!in_array($language, $this->languages)) {
            return false;
        }
        
        $this->language = $language;

        if ($store) {
            $this->setCookie(array('language' => $this->language));
        }
    }

    public function getLanguage ()
    {
        return $this->language;
    }

    public function reload ()
    {
        $this->Cache->reload(true);
        $this->Curl->Cache->reload(true);

        unset($_GET['reload']);
    }

    public function clearData ()
    {
        $this->logged = false;
        $this->user = 0;
        $this->session = '';
        $this->games = array();
    }

    public function logged ()
    {
        if ($this->logged) {
            return $this->logged;
        }

        $cookie = $this->getCookie();

        if (!isset($cookie['user']) || !isset($cookie['session'])) {
            return false;
        }

        return $this->loginSession($cookie['user'], $cookie['session']);
    }

    public function login ($user, $password)
    {
        if (!$user || !$password) {
            return false;
        }

        $this->clearData();

        $Login = $this->Curl->post('login', array('email' => $user, 'password' => $password));

        if (!is_object($Login) || !$Login->id) {
            return false;
        }

        $this->logged = true;
        $this->user = $Login->id;
        $this->session = $Login->session->session;

        $this->Curl->setCookie('ap_session='.$this->session);

        $this->loadGames($Login->id);

        $this->setCookie(array(
            'user' => $this->user,
            'session' => $this->session
        ));

        return $this->logged;
    }

    public function loginSession ($user, $session)
    {
        $this->Curl->setCookie('ap_session='.$session);

        $this->clearData();

        $games = $this->loadGames($user);

        if ($games) {
            $this->logged = true;
            $this->user = $user;
            $this->session = $session;

            $cookie = $this->getCookie();

            if (!$cookie) {
                $this->setCookie(array(
                    'user' => $this->user,
                    'session' => $this->session
                ));
            }
        }

        return $this->logged;
    }

    public function logout () {
        return setcookie('apalabros', '', time() - 3600);
    }

    private function loadGames ($user)
    {
        $Games = $this->Curl->get('users/'.$user.'/games');

        if (!is_object($Games) || !$Games->total) {
            return array();
        }

        $this->games = array(
            'all' => array(),
            'pending' => array(),
            'active' => array(),
            'endend' => array(),
            'turn' => array(),
            'waiting' => array()
        );

        foreach ($Games->list as $Game) {
            if (isset($Game->opponent->facebook_name)) {
                $Game->opponent->name = $Game->opponent->facebook_name;
                $Game->opponent->avatar = 'http://graph.facebook.com/'
                    .$Game->opponent->facebook_id
                    .'/picture';
            } else {
                $Game->opponent->name = $Game->opponent->username;
                $Game->opponent->avatar = '';
            }

            $this->games['all'][$Game->id] = $Game;

            if ($Game->game_status === 'ACTIVE') {
                $this->games['active'][$Game->id] = $Game;

                if ($Game->my_turn) {
                    $this->games['turn'][$Game->id] = $Game;
                } else {
                    $this->games['waiting'][$Game->id] = $Game;
                }
            } else if (in_array($Game->game_status, array('PENDING_FRIENDS_APPROVAL', 'PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE'))) {
                $this->games['pending'][$Game->id] = $Game;
            } else {
                $this->games['ended'][$Game->id] = $Game;
            }
        }

        return $this->games;
    }

    private function _loggedOrDie ()
    {
        if (!$this->logged) {
            throw new \Exception('You are not logged');
        }
    }

    public function getGames ($status = 'turn')
    {
        $this->_loggedOrDie();

        return $this->games[$status];
    }

    public function getGame ($game)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['all'][$game])) {
            return false;
        }

        $Game = $this->Curl->get('users/'.$this->user.'/games/'.$game);

        if (isset($Game->opponent->facebook_name)) {
            $Game->opponent->name = $Game->opponent->facebook_name;
            $Game->opponent->avatar = 'http://graph.facebook.com/'
                .$Game->opponent->facebook_id
                .'/picture';
        } else {
            $Game->opponent->name = $Game->opponent->username;
            $Game->opponent->avatar = '';
        }

        $this->games['all'][$game] = $Game;

        $this->setTiles($game);

        return $Game;
    }

    private function setTiles ($game)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['all'][$game]->board_tiles)) {
            return false;
        }

        $Game = &$this->games['all'][$game];

        if ($Game->board_tiles) {
            $tiles = explode(',', $Game->board_tiles);

            $Game->board_tiles = array();

            foreach ($tiles as $tile) {
                list($letter, $position) = explode('|', $tile);

                $Game->board_tiles[$position] = str_replace('-', '*', $letter);
            }

            ksort($Game->board_tiles);
        }

        if (isset($Game->my_rack_tiles) && $Game->my_rack_tiles) {
            $Game->my_rack_tiles = explode(',', $Game->my_rack_tiles);

            sort($Game->my_rack_tiles, SORT_STRING);

            if (($wildcard = array_search('-', $Game->my_rack_tiles)) !== false) {
                $Game->my_rack_tiles[$wildcard] = '*';
            }
        } else {
            $Game->my_rack_tiles = array();
        }

        return true;
    }

    public function getTiles ($game)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['all'][$game])) {
            return array();
        }

        if (!isset($this->games['all'][$game]->board_tiles)) {
            $this->getGame($game);
        }

        return $this->games['all'][$game]->my_rack_tiles;
    }

    public function getBoard ($game)
    {
        $this->_loggedOrDie();

        if (!$this->games['all'][$game]) {
            return '';
        }

        if (!isset($this->games['all'][$game]->board_tiles)) {
            $this->getGame($game);
        }

        $this->loadWords();

        $Game = $this->games['all'][$game];

        $tiles = $Game->board_tiles;

        $board = '<tr>';

        for ($i = 0; $i < (15 * 15); $i++) {
            if (($i > 0) && (($i % 15) === 0)) {
                $board .= '</tr><tr>';
            }

            if (isset($tiles[$i])) {
                if (strstr($tiles[$i], '*') === false) {
                    $board .= '<td class="tile-35">';
                    $board .= '<span class="letter">'.$tiles[$i].'</span>';
                    $board .= '<span class="points">'.$this->words[$tiles[$i]].'</span>';
                } else {
                    $board .= '<td class="tile-35 wildcard">';
                    $board .= '<span class="letter">'.str_replace('*', '', $tiles[$i]).'</span>';
                    $board .= '<span class="points">0</span>';
                }
            } else {
                $board .= '<td data-position="'.$i.'">&nbsp;';
            }

            $board .= '</td>';
        }

        $board .= '</tr>';

        return $board;
    }

    public function solve ($game)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['all'][$game])) {
            return array();
        }

        if (!isset($this->games['all'][$game]->board_tiles)) {
            $this->getGame($game);
        }

        $Game = $this->games['all'][$game];

        if (!isset($Game->my_rack_tiles) || !$Game->my_rack_tiles) {
            return array();
        }

        if (strtolower($Game->language) !== $this->language) {
            return array();
        }

        $this->setBoardSpaces($Game->id);

        $cache_key = array_merge($Game->my_rack_tiles, $this->board_spaces);

        if ($this->Cache->exists($cache_key)) {
            return $this->Cache->get($cache_key);
        }

        $words = $this->searchWithLetters($Game->my_rack_tiles);

        if (false && $this->board_spaces) {
            foreach ($this->board_spaces as $board_spaces) {
                $words = array_merge($words, $this->searchWithWords($Game->my_rack_tiles, $board_spaces));
            }
        }

        krsort($words);

        $this->Cache->set($cache_key, $words);

        return $words;
    }

    public function play ($game, $post)
    {
        $this->_loggedOrDie();

        if (!isset($post['played_tiles']) || !$post['played_tiles']) {
            return false;
        }

        if (!$this->games['all'][$game]) {
            return false;
        }

        $Game = $this->games['all'][$game];

        if (!in_array($Game->game_status, array('ACTIVE', 'PENDING_FIRST_MOVE', 'PENDING_MY_APPROVAL')) || !$Game->my_turn) {
            return false;
        }

        $played_tiles = '';

        foreach ($post['played_tiles'] as $position => $tile) {
            $played_tiles[] = strtoupper($tile).'|'.$position;
        }

        return $this->Curl->post('users/'.$this->user.'/games/'.$Game->id.'/turns', array(
            'type' => 'PLACE_TILE',
            'played_tiles' => implode(',', $played_tiles)
        ));
    }

    public function turnType ($game, $type, $data = array())
    {
        $this->_loggedOrDie();

        if (!isset($this->games['active'][$game])) {
            return false;
        }

        $data['type'] = $type;

        return $this->Curl->post('users/'.$this->user.'/games/'.$game.'/turns', $data);
    }

    public function setBoardSpaces ($game)
    {
        $this->_loggedOrDie();

        if (!$this->games['all'][$game]) {
            return array();
        }

        if (!isset($this->games['all'][$game]->board_spaces)) {
            $this->getGame($game);
        }

        $Game = &$this->games['all'][$game];

        $this->board_spaces = array();

        if (!$Game->board_tiles) {
            return true;
        }

        $previous = false;
        $left = $right = $top = $bottom = 0;

        for ($i = 0; $i < (15 * 15); $i++) {
            $row = intval($i / 15);
            $left = (($i % 15) === 0) ? 0 : $left;

            if (!isset($Game->board_tiles[$i])) {
                ++$left;
                continue;
            }

            $letter = $Game->board_tiles[$i];

            if ($this->board_spaces) {
                $previous = &$this->board_spaces[count($this->board_spaces) - 1];

                if ($previous['row'] === $row) {
                    if ($left === 0) {
                        $previous['letter'] .= $letter;
                        ++$previous['end'];
                    } else {
                        $previous['right'] = $left;
                    }
                } else {
                    $previous['right'] = (($previous['row'] + 1) * 15) - 1 - $previous['end'];
                }
            }

            if (!$previous || ($left !== 0) || ($previous['row'] !== $row)) {
                $this->board_spaces[] = array(
                    'start' => $i,
                    'end' => $i,
                    'letter' => $letter,
                    'left' => $left,
                    'row' => $row
                );
            }

            $left = 0;
        }

        $previous = &$this->board_spaces[count($this->board_spaces) - 1];
        $previous['right'] = (($previous['row'] + 1) * 15) - 1 - $previous['end'];

        return $this->board_spaces;
    }

    public function searchWithLetters ($tiles) {
        $this->loadLanguage();

        $len_tiles = count($tiles);
        $wildcard = in_array('*', $tiles);
        $words = array();

        for ($i = 0; $i < $this->dictionary_len; $i++) {
            $word = $this->dictionary[$i];

            if ((strlen($word) > $len_tiles) || (strlen($word) < 2)) {
                continue;
            }

            if (!$this->allInArray(str_split_unicode($word), $tiles, $wildcard)) {
                continue;
            }

            $points = $this->getWordPoints($word, $tiles);

            if (!isset($words[$points]) || !in_array($word, $words[$points])) {
                $words[$points][] = $word;
            }
        }

        return $words;
    }

    public function searchWithWords ($tiles, $word) {
        return array();

        $this->loadLanguage();

        $len_tiles = count($tiles);
        $wildcard = in_array('*', $tiles);
        $words = array();

        for ($i = 0; $i < $this->dictionary_len; $i++) {
            $word = $this->dictionary[$i];

            if ((strlen($word) > $len_tiles) || (strlen($word) < 2)) {
                continue;
            }

            if (!$this->allInArray(str_split_unicode($word), $tiles, $wildcard)) {
                continue;
            }

            $points = $this->getWordPoints($word, $tiles);

            if (!isset($words[$points]) || !in_array($word, $words[$points])) {
                $words[$points][] = $word;
            }
        }

        return $words;
    }

    public function getWordPoints ($word, $compare = array())
    {
        if (!$this->words) {
            $this->loadWords();
        }

        $word = str_split_unicode(strtolower($word));
        $full = count($compare);
        $used = 0;
        $points = 0;

        foreach ($word as $letter) {
            if ($compare) {
                if (($key = array_search($letter, $compare)) === false) {
                    continue;
                }

                unset($compare[$key]);

                if (isset($this->words[$letter])) {
                    $points += $this->words[$letter];
                    ++$used;
                }
            } else if (isset($this->words[$letter])) {
                $points += $this->words[$letter];
                ++$used;
            }
        }

        if (($full === 7) && ($used >= 7) && !$compare) {
            $points += 40;
        }

        return $points;
    }

    private function loadLanguage ()
    {
        $this->loadDic();
        $this->loadWords();
    }

    private function loadDic ()
    {
        if ($this->dictionary) {
            return true;
        }

        $file = BASE_PATH.'/languages/'.$this->language.'/dictionary.txt';

        if (!is_file($file)) {
            throw new \Exception(sprintf('File dictionary.txt not exists into %s folder .', basename($file)));
        }

        $this->dictionary = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->dictionary_len = count($this->dictionary);
    }

    private function loadWords ()
    {
        if ($this->words) {
            return true;
        }

        $file = BASE_PATH.'/languages/'.$this->language.'/words.txt';

        if (!is_file($file)) {
            throw new \Exception(sprintf('File words.txt not exists into %s folder .', basename($file)));
        }

        $this->words = array();
        $words = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($words as $word) {
            list($word, $value) = explode(',', $word);
            $this->words[$word] = $value;
        }
    }

    private function allInArray ($array1, $array2, $wildcard)
    {
        foreach ($array1 as $value) { 
            if (($key = array_search($value, $array2, true)) === false) {
                if ($wildcard && isset($this->words[$value])) {
                    $wildcard = false;
                } else {
                    return false;
                }
            }

            unset($array2[$key]);
        }

        return true;
    }
}
