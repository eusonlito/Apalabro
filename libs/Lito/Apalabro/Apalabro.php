<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Apalabro {
    private $server = 'http://api.apalabrados.com/api/';
    private $logged = false;
    private $user = 0;
    private $session = '';
    private $language = 'es';
    private $dictionary = array();
    private $words = array();
    private $games = array();
    private $my_rack_tiles = array();

    private $Curl;
    private $Debug;

    public $Cache;

    public function __construct ()
    {
        $this->Cache = new Cache;
        $this->Curl = new Curl;
        $this->Debug = new Debug;

        $this->Curl->init($this->server);
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

    public function setLanguage ($language)
    {

        $folder = BASE_PATH.'/languages/'.$language;

        if (!is_dir($folder)) {
            throw new \Exception(sprintf('Language folder %s does not exists.', $folder));
        }

        $this->language = $language;
    }

    public function getLanguage ()
    {
        return $this->language;
    }

    public function reload ()
    {
        $this->Cache->reload(true);
        $this->Curl->Cache->reload(true);
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

        if (!isset($_COOKIE['apalabros']) || !$_COOKIE['apalabros']) {
            return false;
        }

        $cookie = unserialize(gzinflate($_COOKIE['apalabros']));

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

        setCookie('apalabros', gzdeflate(serialize(array(
            'user' => $this->user,
            'session' => $this->session
        ))));

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

            if (!$_COOKIE['apalabros']) {
                setCookie('apalabros', gzdeflate(serialize(array(
                    'user' => $this->user,
                    'session' => $this->session
                ))));
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
            return false;
        }

        $this->games = array(
            'all' => array(),
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
        if (!isset($this->games['all'][$game]->board_tiles)) {
            return false;
        }

        $Game = &$this->games['all'][$game];

        $tiles = explode(',', $Game->board_tiles);

        $Game->board_tiles = array();

        foreach ($tiles as $tile) {
            list($letter, $position) = explode('|', $tile);

            $Game->board_tiles[$position] = str_replace('-', '*', $letter);
        }

        ksort($Game->board_tiles);

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
        if (!isset($this->games['all'][$game])) {
            return false;
        }

        if (!isset($this->games['all'][$game]->board_tiles)) {
            $this->getGame($game);
        }

        return $this->games['all'][$game]->my_rack_tiles;
    }

    public function getBoard ($game)
    {
        if (!$this->games['all'][$game]) {
            return false;
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
        if (!$this->games['all'][$game]) {
            return false;
        }

        if (!isset($this->games['all'][$game]->board_tiles)) {
            $this->getGame($game);
        }

        $Game = $this->games['all'][$game];

        if (!isset($Game->my_rack_tiles) || !$Game->my_rack_tiles) {
            return false;
        }

        if ($this->Cache->exists($Game->my_rack_tiles)) {
            return $this->Cache->get($Game->my_rack_tiles);
        }

        $this->loadLanguage();

        $len_tiles = count($Game->my_rack_tiles);
        $len_dic = count($this->dictionary);
        $wildcard = in_array('*', $Game->my_rack_tiles);
        $words = array();

        for ($i = 0; $i < $len_dic; $i++) {
            $word = $this->dictionary[$i];

            if ((strlen($word) > $len_tiles) || (strlen($word) < 2)) {
                continue;
            }

            if (!$this->allInArray(str_split($word), $Game->my_rack_tiles, $wildcard)) {
                continue;
            }

            $points = $this->getWordPoints($word, $Game->my_rack_tiles);

            if (!isset($words[$points]) || !in_array($word, $words[$points])) {
                $words[$points][] = $word;
            }
        }

        krsort($words);

        $this->Cache->set($Game->my_rack_tiles, $words);

        return $words;
    }

    public function setBoardSpaces ($game)
    {
        if (!$this->games['all'][$game]) {
            return false;
        }

        if (!isset($this->games['all'][$game]->board_spaces)) {
            $this->getGame($game);
        }

        $Game = &$this->games['all'][$game];

        $Game->board_spaces = array();

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

            if ($Game->board_spaces) {
                $previous = &$Game->board_spaces[count($Game->board_spaces) - 1];

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
                $Game->board_spaces[] = array(
                    'start' => $i,
                    'end' => $i,
                    'letter' => $letter,
                    'left' => $left,
                    'row' => $row
                );
            }

            $left = 0;
        }

        $previous = &$Game->board_spaces[count($Game->board_spaces) - 1];
        $previous['right'] = (($previous['row'] + 1) * 15) - 1 - $previous['end'];

        return $Game->board_spaces;
    }

    public function getWordPoints ($word, $compare = array())
    {
        if (!$this->words) {
            $this->loadWords();
        }

        $word = str_split(strtolower($word));
        $points = 0;

        foreach ($word as $letter) {
            if ($compare) {
                if (($key = array_search($letter, $compare)) === false) {
                    continue;
                }

                unset($compare[$key]);

                if (isset($this->words[$letter])) {
                    $points += $this->words[$letter];
                }
            } else if (isset($this->words[$letter])) {
                $points += $this->words[$letter];
            }
        }

        return $points;
    }

    public function turnType ($game, $type)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['active'][$game])) {
            return false;
        }

        $Game = $this->Curl->post('users/'.$this->user.'/games/'.$game.'/turns', array('type' => $type));

        return $Game;
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
