<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Apalabro {
    private $server = 'http://api.apalabrados.com/api/';
    private $logged = false;
    private $user = 0;
    private $session = '';
    private $language = '';
    private $languages = array();
    private $dictionary = array();
    private $words = array();
    private $games = array();
    private $my_rack_tiles = array();
    private $board_points = array();

    private $Cookie;
    private $Curl;
    private $Debug;
    private $Timer;

    public $Cache;

    public function __construct ()
    {
        global $Timer;

        $this->Cache = new Cache;
        $this->Cookie = new Cookie;
        $this->Curl = new Curl;
        $this->Debug = new Debug;

        $this->Timer = $Timer;

        $this->Curl->init($this->server);

        $this->setLanguages();
    }

    public function setDebug ($debug)
    {
        $this->Debug->setDebug($debug);
        $this->Curl->Debug->setDebug($debug);
    }

    public function setLanguages ()
    {
        $this->languages = array();

        foreach (glob(BASE_PATH.'/languages/*', GLOB_ONLYDIR) as $language) {
            if (is_file($language.'/dictionary.php') || is_file($language.'/words.php')) {
                $this->languages[] = basename($language);
            }
        }
    }

    public function setLanguage ($language)
    {
        $this->language = '';

        $language = mb_strtolower($language);

        if (in_array($language, $this->languages)) {
            $this->language = $language;
        }
    }

    public function getLanguages ()
    {
        return $this->languages;
    }

    public function getLanguage ()
    {
        return $this->language;
    }

    public function debug ($text, $info = true)
    {
        $this->Debug->show($text, $info);
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

        $cookie = $this->Cookie->get();

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

        $Login = $this->Curl->post('login', array(
            'email' => $user,
            'password' => $password
        ));

        if (!is_object($Login) || !isset($Login->id)) {
            return false;
        }

        $this->logged = true;
        $this->user = $Login->id;
        $this->session = $Login->session->session;

        $this->Curl->setCookie('ap_session='.$this->session);

        $this->loadGames($Login->id);

        $this->Cookie->set(array(
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

            $cookie = $this->Cookie->get();

            if (!$cookie) {
                $this->setCookie(array(
                    'user' => $this->user,
                    'session' => $this->session
                ));
            }
        }

        return $this->logged;
    }

    public function loginFacebook ($email)
    {
        $this->clearData();

        $User = $this->Curl->post('emails', $email);

        if (!is_object($User) || ($User->total !== 1)) {
            return false;
        }

        $Login = $this->Curl->post('login', array(
            'id' => $User->list[0]->id,
            'username' => $User->list[0]->username,
            'email' => $User->list[0]->email,
            'facebook_id' => $User->list[0]->facebook_id,
        ));

        if (!is_object($Login) || !isset($Login->id)) {
            return false;
        }

        $this->logged = true;
        $this->user = $Login->id;
        $this->session = $Login->session->session;

        $this->Curl->setCookie('ap_session='.$this->session);

        $this->loadGames($Login->id);

        $this->Cookie->set(array(
            'user' => $this->user,
            'session' => $this->session
        ));

        return $this->logged;
    }

    public function logout ()
    {
        $this->Cookie->set('', -3600);
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

            if (in_array($Game->game_status, array('ACTIVE', 'PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE'))) {
                $this->games['active'][$Game->id] = $Game;

                if ($Game->my_turn) {
                    $this->games['turn'][$Game->id] = $Game;
                } else {
                    $this->games['waiting'][$Game->id] = $Game;
                }
            } else if ($Game->game_status == 'PENDING_FRIENDS_APPROVAL') {
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

        if ($this->Cache->exists('game-'.$game)) {
            $Game = $this->Cache->get('game-'.$game);
        } else {
            $Game = $this->Curl->get('users/'.$this->user.'/games/'.$game);

            if ($Game->game_status === 'ENDED') {
                $this->Cache->set('game-'.$game, $Game);
            }
        }

        if (isset($Game->opponent->facebook_name)) {
            $Game->opponent->name = $Game->opponent->facebook_name;
            $Game->opponent->avatar = 'http://graph.facebook.com/'
                .$Game->opponent->facebook_id
                .'/picture';
        } else {
            $Game->opponent->name = $Game->opponent->username;
            $Game->opponent->avatar = '';
        }

        if (in_array($Game->game_status, array('ACTIVE', 'PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE'))) {
            $Game->active = true;
        } else {
            $Game->active = false;
        }

        $this->games['all'][$game] = $Game;

        $this->setTiles($game);
        $this->setLanguage($Game->language);

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

    public function solve ($game, $expression = '')
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

        $cache_key = implode('', $Game->my_rack_tiles).$expression;

        if ($this->Cache->exists($cache_key)) {
            return $this->Cache->get($cache_key);
        }

        if ($expression) {
            $words = $this->searchWordsExpression($Game->my_rack_tiles, $expression);
        } else {
            $words = $this->searchWords($Game->my_rack_tiles);
        }

        $this->Cache->set($cache_key, $words);

        return $words;
    }

    public function playGame ($game, $post)
    {
        $this->_loggedOrDie();

        if (!isset($post['played_tiles']) || !$post['played_tiles']) {
            return false;
        }

        if (!$this->games['all'][$game]) {
            return false;
        }

        $Game = $this->games['all'][$game];

        if (!$Game->active || !$Game->my_turn) {
            return false;
        }

        $played_tiles = '';

        foreach ($post['played_tiles'] as $cell => $letter) {
            $played_tiles[] = strtoupper($letter).'|'.$cell;
        }

        return $this->Curl->post('users/'.$this->user.'/games/'.$Game->id.'/turns', array(
            'type' => 'PLACE_TILE',
            'played_tiles' => implode(',', $played_tiles)
        ));
    }

    public function newGame ($language)
    {
        $this->_loggedOrDie();

        if (!in_array($language, $this->languages)) {
            return false;
        }

        return $this->Curl->post('users/'.$this->user.'/games', array(
            'language' => strtoupper($language)
        ));
    }

    public function swapTiles ($game, $tiles)
    {
        $this->_loggedOrDie();

        if (!$tiles || !isset($this->games['active'][$game])) {
            return false;
        }

        return $this->turnType($game, 'SWAP', array(
            'played_tiles' => implode(',', $tiles)
        ));
    }

    public function passTurn ($game)
    {
        $this->_loggedOrDie();

        return $this->turnType($game, 'PASS');
    }

    public function resignGame ($game)
    {
        $this->_loggedOrDie();

        return $this->turnType($game, 'RESIGN');
    }

    public function turnType ($game, $type, $data = null)
    {
        $this->_loggedOrDie();

        if (!isset($this->games['active'][$game])) {
            return false;
        }

        $data['type'] = $type;

        return $this->Curl->post('users/'.$this->user.'/games/'.$game.'/turns', ($data ?: null));
    }

    public function searchWords ($tiles)
    {
        $this->loadLanguage();

        $this->Timer->mark('INI: Apalabro->searchWords');

        $len_tiles = count($tiles);
        $wildcard = in_array('*', $tiles);
        $words = array();

        foreach ($this->dictionary as $word) {
            if ((mb_strlen($word) > $len_tiles) || (mb_strlen($word) < 2)) {
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

        krsort($words);

        $this->Timer->mark('END: Apalabro->searchWords');

        return $words;
    }

    public function searchWordsExpression ($tiles, $expression = '')
    {
        if (!$expression) {
            return array();
        }

        $this->loadLanguage();

        $this->Timer->mark('INI: Apalabro->searchWordsExpression');

        $expression = str_replace('/', '', mb_strtolower($expression));
        $expression_tiles = str_split_unicode(preg_replace('/[^a-zñ]/', '', $expression));

        if ($expression_tiles) {
            $tiles = array_merge($tiles, $expression_tiles);
        }

        $wildcard = in_array('*', $tiles);
        $words = array();

        foreach ($this->dictionary as $word) {
            if (mb_strlen($word) < 2) {
                continue;
            }

            if (!preg_match('/'.$expression.'/', $word)) {
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

        krsort($words);

        $this->Timer->mark('END: Apalabro->searchWordsExpression');

        return $words;
    }

    public function getWordPoints ($word, $compare = array())
    {
        if (!$this->words) {
            $this->loadWords();
        }

        $word = str_split_unicode(mb_strtolower($word));
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

    public function getPlayPoints ($game, $tiles)
    {
        $this->_loggedOrDie();

        if (!$this->games['all'][$game]) {
            return array();
        }

        foreach ($tiles as $cell => $letter) {
            if (preg_match('/^[0-9]+$/', $cell) && preg_match('/^[a-zñ\*]+$/', $letter)) {
                $tiles[$cell] = mb_strtolower($letter);
            } else {
                unset($tiles[$cell]);
            }
        }

        if (!$tiles) {
            return array();
        }

        $this->loadBoardSpaces($game, $tiles);

        $Game = $this->games['all'][$game];

        $matched = $Game->board_spaces['added'];

        if (!$matched) {
            return array();
        }

        $words = array();

        foreach ($matched as $word) {
            $words[] = implode('', $word['letters']);
        }

        $words = array_unique($words);

        $check = $this->Curl->post('dictionaries/'.mb_strtoupper($this->language), mb_strtoupper(implode(',', $words)));

        $this->loadBoardPoints();

        foreach ($matched as &$word) {
            if (!in_array(mb_strtoupper(implode('', $word['letters'])), $check->ok)) {
                continue;
            }

            $word['ok'] = true;

            foreach ($word['added'] as $cell) {
                $key = array_search($cell, $word['cells']);

                $letter = $word['letters'][$key];

                if (($word['points'][$key] > 0) && isset($this->board_points['letter'][$cell])) {
                    $word['points'][$key] *= $this->board_points['letter'][$cell];
                }

                if (isset($this->board_points['word'][$cell])) {
                    foreach (array_keys($word['points']) as $key) {
                        $word['points'][$key] *= $this->board_points['word'][$cell];
                    }
                }
            }

            if (count($word['added']) === 7) {
                $word['points'][] += 40;
            }
        }

        unset($word);

        return $matched;
    }

    private function loadBoardPoints ()
    {
        if ($this->board_points) {
            return true;
        }

        $this->board_points = include (BASE_PATH.'aux/board-points.php');
    }

    public function loadBoardSpaces ($game, $added = array())
    {
        $this->_loggedOrDie();

        if (!$this->games['all'][$game]) {
            return array();
        }

        $Game = &$this->games['all'][$game];

        if (isset($Game->board_spaces)) {
            return $Game->board_spaces;
        }

        if (!isset($Game->board_tiles)) {
            $this->getGame($game);

            $Game = &$this->games['all'][$game];
        }

        $Game->board_spaces = array();

        if (!$Game->board_tiles && !$added) {
            return array();
        }

        $this->Timer->mark('INI: Apalabro->setBoardSpaces');

        $previous = false;
        $left = $right = $top = $bottom = 0;

        $bt = $Game->board_tiles;

        if ($added) {
            $Game->board_spaces['added'] = array();
            $bt += $added;
        }

        for ($i = 0; $i < (15 * 15); ) {
            if (!isset($bt[$i])) {
                ++$i;
                continue;
            }

            $word = array(
                'letters' => array(),
                'cells' => array(),
                'points' => array(),
                'wildcards' => array()
            );

            do {
                $letter = $bt[$i];

                if ($wildcard = (strstr($letter, '*') !== false)) {
                    $letter = str_replace('*', '', $letter);
                }

                $word['letters'][] = $letter;
                $word['cells'][] = $i;

                if ($wildcard) {
                    $word['points'][] = 0;
                    $word['wildcards'][] = $i;
                } else {
                    $word['points'][] = $this->getWordPoints($letter);
                    $word['wildcards'][] = false;
                }
            } while (isset($bt[++$i]) && (($i % 15) !== 0));

            foreach ($word['cells'] as $cell) {
                $Game->board_spaces[$cell] = array(
                    'x' => $word
                );

                if (isset($added[$cell]) && (count($word['letters']) > 1)) {
                    $key = implode('|', $word['cells']);

                    if (!isset($Game->board_spaces['added'][$key])) {
                        $Game->board_spaces['added'][$key] = $word;
                        $Game->board_spaces['added'][$key]['added'] = array();
                    }

                    $Game->board_spaces['added'][$key]['added'][] = $cell;
                }
            }
        }

        for ($i = 0; $i < (15 * 15); $i++) {
            if (!isset($bt[$i]) || isset($Game->board_spaces[$i]['y'])) {
                continue;
            }

            $y = $i;
            $word = array(
                'letters' => array(),
                'cells' => array(),
                'points' => array(),
                'wildcards' => array()
            );

            do {
                $letter = $bt[$y];

                if ($wildcard = (strstr($letter, '*') !== false)) {
                    $letter = str_replace('*', '', $letter);
                }

                $word['letters'][] = $letter;
                $word['cells'][] = $y;

                if ($wildcard) {
                    $word['points'][] = 0;
                    $word['wildcards'][] = $y;
                } else {
                    $word['points'][] = $this->getWordPoints($letter);
                    $word['wildcards'][] = false;
                }

                $y += 15;
            } while (isset($bt[$y]));

            foreach ($word['cells'] as $cell) {
                $Game->board_spaces[$cell]['y'] = $word;

                if (isset($added[$cell]) && (count($word['letters']) > 1)) {
                    $key = implode('|', $word['cells']);

                    if (!isset($Game->board_spaces['added'][$key])) {
                        $Game->board_spaces['added'][$key] = $word;
                        $Game->board_spaces['added'][$key]['added'] = array();
                    }

                    $Game->board_spaces['added'][$key]['added'][] = $cell;
                }
            }
        }

        $this->Timer->mark('END: Apalabro->setBoardSpaces');
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

        $this->dictionary = array();

        $file = BASE_PATH.'/languages/'.$this->language.'/dictionary.php';

        if (!is_file($file)) {
            return false;
        }

        $this->Timer->mark('INI: Apalabro->loadDic '.$this->language);

        $this->dictionary = include ($file);

        $this->Timer->mark('END: Apalabro->loadDic '.$this->language);
    }

    private function loadWords ()
    {
        if ($this->words) {
            return true;
        }

        $this->words = array();

        $file = BASE_PATH.'/languages/'.$this->language.'/words.php';

        if (!is_file($file)) {
            return false;
        }

        $this->words = include ($file);
    }

    public function mergeDic ($language, $file, $new)
    {
        if (!is_file($file)) {
            return false;
        }

        $this->language = mb_strtolower($language);

        $this->loadDic();

        $info = pathinfo($new);

        if (!isset($info['extension'])) {
            $new .= '.php';
        } else if (mb_strtolower($info['extension']) !== 'php') {
            $new = preg_replace('/\.[^\.]+$/', '.php', $new);
        }

        $new = BASE_PATH.'/languages/'.$this->language.'/'.$new;

        if ((is_file($new) && !is_writable($new)) || (!is_file($new) && !is_writable(dirname($new)))) {
            return false;
        }

        $dictionary = $this->txtDic2array($file);

        if ($this->dictionary) {
            // foreach is ALWAYS faster than array_merge
            foreach ($this->dictionary as $word) {
                $dictionary[] = $word;
            }

            $dictionary = array_unique($dictionary);
        }

        return file_put_contents($new, "<?php return array('".implode("','", $dictionary)."'); ?>");
    }

    private function txtDic2array ($file)
    {
        if (!is_file($file)) {
            return array();
        }

        $dictionary = trim(mb_strtolower(file_get_contents($file)));
        $dictionary = strtr(utf8_decode($dictionary), utf8_decode('àáâãäçèéêëìíîïòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiiooooouuuuyyaaaaaceeeeiiiiooooouuuuy');
        $dictionary = str_replace("\n", " ", preg_replace('/[^a-zñ\s]/', '', $dictionary));
        $dictionary = array_unique(explode(' ', trim(preg_replace('/\s+/', ' ', $dictionary))));

        sort($dictionary);

        return $dictionary;
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
