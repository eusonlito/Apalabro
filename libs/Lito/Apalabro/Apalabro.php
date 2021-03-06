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
    private $points = array();
    private $quantity = array();
    private $games = array();
    private $board_points = array();

    private $Game;
    private $Cookie;
    private $Curl;
    private $Debug;
    private $Timer;

    public $Cache;

    public function __construct ()
    {
        $this->setLanguages();
    }

    public function setTimer ($Timer)
    {
        $this->Timer = &$Timer;
    }

    public function setCache ($Cache)
    {
        $this->Cache = $Cache;
    }

    public function setDebug ($Debug, $function)
    {
        if (is_object($Debug) && method_exists($Debug, $function)) {
            $Debug->function = $function;
            $this->Debug = $Debug;
        }
    }

    public function debug ($text, $trace = true)
    {
        if ($this->Debug) {
            $this->Debug->{$this->Debug->function}($text, $trace);
        }
    }

    public function setCurl ($Curl)
    {
        $this->Curl = $Curl;

        $this->Curl->init($this->server);

        $this->Curl->setOption(CURLOPT_USERAGENT, 'Android/4.0.4 Apalabro/1.4.1.3');
        $this->Curl->setOption(CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Connection: Keep-Alive'
        ));

        $this->Curl->setJson(true);
    }

    public function setCookie ($Cookie)
    {
        $this->Cookie = $Cookie;
    }

    public function setLanguages ()
    {
        $this->languages = array();

        foreach (glob(BASE_PATH.'/languages/*', GLOB_ONLYDIR) as $language) {
            if (is_file($language.'/dictionary.php') && is_file($language.'/points.php')) {
                $this->languages[] = basename($language);
            }
        }
    }

    public function setLanguage ($language)
    {
        $this->language = '';

        $language = mb_strtolower($language);

        if (in_array($language, $this->languages, true)) {
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

    public function reload ()
    {
        if ($this->Cache) {
            $this->Cache->reload(true);
        }

        if ($this->Curl->Cache) {
            $this->Curl->Cache->reload(true);
        }

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

        if (!isset($cookie['user']) || empty($cookie['user'])
        || !isset($cookie['session']) || empty($cookie['session'])) {
            return false;
        }

        return $this->loginSession($cookie['user'], $cookie['session']);
    }

    public function login ($user, $password)
    {
        if (empty($user) || empty($password)) {
            return false;
        }

        $this->clearData();

        $this->Curl->setHeader(true);
        $this->Curl->setOption(CURLOPT_FAILONERROR, false);

        $Login = $this->Curl->post('login', array(
            'email' => $user,
            'password' => $password
        ));

        $this->Curl->setHeader(false);

        if (!is_object($Login) || !isset($Login->id)) {
            return $Login;
        }

        $header = $this->Curl->getHeader();

        preg_match('/ap_session=[a-z0-9]+/', $header, $session);

        $this->logged = true;
        $this->user = $Login->id;
        $this->session = $session[0];

        $this->Curl->setCookie($this->session);

        $this->Cookie->set(array(
            'user' => $this->user,
            'session' => $this->session
        ));

        return $this->logged;
    }

    public function loginSession ($user, $session)
    {
        $this->Curl->setCookie($session);

        $this->clearData();

        $this->logged = true;
        $this->user = $user;
        $this->session = $session;

        $cookie = $this->Cookie->get();

        if (!isset($cookie['user']) || empty($cookie['user'])
        || !isset($cookie['session']) || empty($cookie['session'])) {
            $this->Cookie->set(array(
                'user' => $this->user,
                'session' => $this->session
            ));
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

        $this->Curl->setCookie($this->session);

        $this->Cookie->set(array(
            'user' => $this->user,
            'session' => $this->session
        ));

        return $this->logged;
    }

    public function logout ()
    {
        $this->Cookie->set(array(
            'user' => '',
            'session' => ''
        ));
    }

    private function _loggedOrDie ()
    {
        if (empty($this->logged)) {
            throw new \Exception('You are not logged');
        }
    }

    public function getUser ($user = '')
    {
        $this->_loggedOrDie();

        if ($user) {
            $User = $this->Curl->get('users/'.$this->user.'/users/'.$user);
        } else {
            $User = $this->Curl->get('users/'.$this->user);
        }

        if (!is_object($User) || empty($User->id)) {
            return false;
        }

        $User = $this->setFacebookInfo($User);

        return $User;
    }

    public function getUserLogged ()
    {
        $this->_loggedOrDie();

        return $this->user;
    }

    public function setFacebookInfo ($User) {
        if (!is_object($User)) {
            return $User;
        }

        $User->facebook_public = true;

        if (isset($User->facebook_id) && isset($User->facebook_name) && $User->fb_show_name && $User->facebook_name) {
            $User->name = $User->facebook_name;
        } else {
            $User->name = $User->username;
            $User->facebook_public = false;
        }

        if (isset($User->facebook_id) && $User->fb_show_picture) {
            $User->avatar = 'http://graph.facebook.com/'
                .$User->facebook_id
                .'/picture';
        } else {
            $User->avatar = '';
            $User->facebook_public = false;
        }

        return $User;
    }

    public function myUser ($user = '')
    {
        $this->_loggedOrDie();

        return $user ? ($this->user == $user) : $this->user;
    }

    public function addFriend ($user)
    {
        $this->_loggedOrDie();

        return $this->Curl->post('users/'.$this->user.'/favorites', array(
            'id' => $user
        ));
    }

    public function removeFriend ($user)
    {
        $this->_loggedOrDie();

        return $this->Curl->custom('DELETE', 'users/'.$this->user.'/favorites/'.$user);
    }

    public function getFriends ()
    {
        $this->_loggedOrDie();

        $Friends = $this->Curl->get('users/'.$this->user.'/friends');

        if (!is_object($Friends) || empty($Friends->list)) {
            return array();
        }

        foreach ($Friends->list as &$Friend) {
            $Friend->friend = $this->setFacebookInfo($Friend->friend);
        }

        unset($Friend);

        return $Friends->list;
    }

    public function searchUsers ($filter)
    {
        $this->_loggedOrDie();

        $filter = urlencode($filter);

        $Users = $this->Curl->get('search?email='.$filter.'&username='.$filter);

        if (!is_object($Users) || empty($Users->list)) {
            return array();
        }

        foreach ($Users->list as &$User) {
            $User = $this->setFacebookInfo($User);
        }

        unset($User);

        return $Users->list;
    }

    public function loadGames ()
    {
        $this->_loggedOrDie();

        $this->games = array(
            'all' => array(),
            'pending' => array(),
            'active' => array(),
            'ended' => array(),
            'turn' => array(),
            'waiting' => array()
        );

        $Games = $this->Curl->get('users/'.$this->user.'/games');

        if (!is_object($Games) || empty($Games->total)) {
            return array();
        }

        foreach ($Games->list as $Game) {
            if ($Game->game_status === 'RANDOM') {
                continue;
            }

            $Game->opponent = $this->setFacebookInfo($Game->opponent);

            $this->games['all'][$Game->id] = $Game;

            if (in_array($Game->game_status, array('ACTIVE', 'PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE'), true)) {
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

    public function getGames ($status = '')
    {
        $this->_loggedOrDie();

        if (empty($this->games)) {
            $this->loadGames();
        }

        return ($status) ? $this->games[$status] : $this->games;
    }

    public function preloadGame ($game)
    {
        $this->_loggedOrDie();

        if ($this->Cache && $this->Cache->exists('game-'.$game)) {
            $Game = $this->Cache->get('game-'.$game);
        } else {
            $Game = $this->Curl->get('users/'.$this->user.'/games/'.$game);

            if (!is_object($Game) || empty($Game->id)) {
                return false;
            }

            if ($this->Cache && ($Game->game_status === 'ENDED')) {
                $this->Cache->set('game-'.$game, $Game);
            }
        }

        $Game->opponent = $this->setFacebookInfo($Game->opponent);

        if (in_array($Game->game_status, array('ACTIVE', 'PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE', 'PENDING_FRIENDS_APPROVAL'), true)) {
            $Game->active = true;
        } else {
            $Game->active = false;
        }

        $this->Game = $Game;

        return $Game;
    }

    public function getGame ($game, $reload = false)
    {
        if (empty($this->Game) || ($this->Game->id != $game) || $reload) {
            $this->preloadGame($game);
        }

        $this->setTiles();
        $this->setLanguage($this->Game->language);
        $this->loadLanguage();

        return $this->Game;
    }

    public function getValidWords ()
    {
        $this->_loggedOrDie();

        if (empty($this->language)) {
            return array();
        }

        return array_keys($this->points);
    }

    private function setTiles ()
    {
        $this->_loggedOrDie();

        $Game = &$this->Game;

        if ($Game->board_tiles) {
            $tiles = explode(',', mb_strtolower($Game->board_tiles));

            $Game->board_tiles = array();

            foreach ($tiles as $tile) {
                list($letter, $position) = explode('|', $tile);

                $Game->board_tiles[$position] = str_replace('-', '*', $letter);
            }

            ksort($Game->board_tiles);
        } else {
            $Game->board_tiles = array();
        }

        if (isset($Game->my_rack_tiles) && is_string($Game->my_rack_tiles)) {
            $Game->my_rack_tiles = explode(',', mb_strtolower($Game->my_rack_tiles));

            sort($Game->my_rack_tiles, SORT_STRING);

            if ($wildcards = array_keys($Game->my_rack_tiles, '-')) {
                foreach ($wildcards as $wildcard) {
                    $Game->my_rack_tiles[$wildcard] = '*';
                }
            }
        } else {
            $Game->my_rack_tiles = array();
        }

        return true;
    }

    public function getTiles ()
    {
        $this->_loggedOrDie();

        if (!isset($this->Game->board_tiles)) {
            $this->getGame($game);
        }

        return $this->Game->my_rack_tiles;
    }

    public function getBoard ($highlights = array())
    {
        $this->_loggedOrDie();

        $tiles = $this->Game->board_tiles;

        if (($highlights === true) && isset($this->Game->last_turn->played_tiles)) {
            $highlights = explode(',', $this->Game->last_turn->played_tiles);
        } else if (is_string($highlights)) {
            $highlights = explode(',', $highlights);
        }

        $board = '<tr>';

        for ($i = 0; $i < (15 * 15); $i++) {
            if (($i > 0) && (($i % 15) === 0)) {
                $board .= '</tr><tr>';
            }

            $tile = isset($tiles[$i]) ? $tiles[$i] : false;

            if ($tile) {
                $highlight = ($highlights && in_array($i, $highlights)) ? ' highlight' : '';

                if (strstr($tile, '*') === false) {
                    $board .= '<td class="tile-35'.$highlight.'">';
                    $board .= '<span class="letter">'.$tile.'</span>';
                    $board .= '<span class="points">'.$this->points[$tile].'</span>';
                } else {
                    $board .= '<td class="tile-35'.$highlight.' wildcard">';
                    $board .= '<span class="letter">'.str_replace('*', '', $tile).'</span>';
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

    public function solve ($expression = '', $tiles = array())
    {
        $this->_loggedOrDie();

        $Game = $this->Game;

        if (!isset($Game->my_rack_tiles) || empty($Game->my_rack_tiles)) {
            return array();
        }

        if ($tiles) {
            $tiles = array_intersect($tiles, $Game->my_rack_tiles);
        } else {
            $tiles = $Game->my_rack_tiles;
        }

        $cache_key = implode('', $tiles).$expression;

        if ($this->Cache && $this->Cache->exists($cache_key)) {
            return $this->Cache->get($cache_key);
        }

        if ($expression) {
            $words = $this->searchWordsExpression($tiles, $expression);
        } else {
            $words = $this->searchWords($tiles);
        }

        if ($this->Cache) {
            $this->Cache->set($cache_key, $words);
        }

        return $words;
    }

    public function getRemainingTiles ()
    {
        $this->_loggedOrDie();

        $Game = $this->Game;

        if (empty($this->quantity)) {
            $this->loadLetters();
        }

        $tiles = array();

        if ($Game->board_tiles) {
            $tiles = $Game->board_tiles;
        }

        if ($Game->my_rack_tiles) {
            $tiles = array_merge($tiles, $Game->my_rack_tiles);
        }

        $remaining = $this->quantity;
        $wildcards = 2;

        foreach ($tiles as $tile) {
            if (isset($remaining[$tile])) {
                --$remaining[$tile];
            } else if (strstr($tile, '*')) {
                --$wildcards;
            }
        }

        foreach ($remaining as $tile => $quantity) {
            if ($quantity < 1) {
                unset($remaining[$tile]);
            }
        }

        if ($wildcards) {
            $remaining['*'] = $wildcards;
        }

        arsort($remaining);

        return $remaining;
    }

    public function getChat ()
    {
        $this->_loggedOrDie();

        $Chat = $this->Curl->get('users/'.$this->user.'/games/'.$this->Game->id.'/chat?all=true');

        if (!is_object($Chat) || empty($Chat->list)) {
            return array();
        }

        return $Chat->list;
    }

    public function setChat ($message)
    {
        $this->_loggedOrDie();

        $message = trim($message);

        if (empty($message)) {
            return false;
        }

        return $this->Curl->post('users/'.$this->user.'/games/'.$this->Game->id.'/chat', array(
            'message' => $message
        ));
    }

    public function resetChat ()
    {
        $this->_loggedOrDie();

        return $this->Curl->get('users/'.$this->user.'/games/'.$this->Game->id.'/chat?reset=true');
    }

    public function playGame ($post)
    {
        $this->_loggedOrDie();

        if (!isset($post['played_tiles']) || empty($post['played_tiles'])) {
            return false;
        }

        if (empty($this->Game->active) || empty($this->Game->my_turn)) {
            return false;
        }

        $played_tiles = '';

        foreach ($post['played_tiles'] as $cell => $letter) {
            $played_tiles[] = strtoupper($letter).'|'.$cell;
        }

        return $this->Curl->post('users/'.$this->user.'/games/'.$this->Game->id.'/turns', array(
            'type' => 'PLACE_TILE',
            'played_tiles' => implode(',', $played_tiles)
        ));
    }

    public function newGame ($language, $user = '')
    {
        $this->_loggedOrDie();

        if (!in_array($language, $this->languages, true)) {
            return false;
        }

        $user = intval($user);
        $data = array('language' => strtoupper($language));

        if ($user && preg_match('/^[0-9]+$/', $user)) {
            $data['opponent'] = array('id' => $user);
        }

        return $this->Curl->post('users/'.$this->user.'/games', $data);
    }

    public function swapTiles ($tiles)
    {
        $this->_loggedOrDie();

        if (empty($tiles)) {
            return false;
        }

        return $this->turnType('SWAP', array(
            'played_tiles' => implode(',', $tiles)
        ));
    }

    public function passTurn ()
    {
        $this->_loggedOrDie();

        return $this->turnType( 'PASS');
    }

    public function resignGame ()
    {
        $this->_loggedOrDie();

        return $this->turnType('RESIGN');
    }

    public function turnType ($type, $data = null)
    {
        $this->_loggedOrDie();

        if (empty($this->Game->active)) {
            return false;
        }

        $data['type'] = $type;

        return $this->Curl->post('users/'.$this->user.'/games/'.$this->Game->id.'/turns', ($data ?: null));
    }

    public function searchWords ($tiles)
    {
        $this->_loggedOrDie();

        $this->Timer->mark('INI: Apalabro->searchWords');

        $len_tiles = count($tiles);
        $wildcards = count(array_keys($tiles, '*'));
        $words = array();

        foreach ($this->dictionary as $word) {
            if (!$this->stringInArray($word, $tiles, $wildcards)) {
                continue;
            }

            $points = $this->getWordPoints($word, $tiles);

            if (!isset($words[$points])) {
                $words[$points] = array($word);
            } else if (!in_array($word, $words[$points])) {
                $words[$points][] = $word;
            }
        }

        foreach ($words as $points => $list) {
            $words[$points] = array_slice($list, 0, 20);
        }

        krsort($words);

        $this->Timer->mark('END: Apalabro->searchWords');

        return $words;
    }

    public function searchWordsExpression ($tiles, $expression = '')
    {
        $this->_loggedOrDie();

        if (empty($expression)) {
            return array();
        }

        $this->Timer->mark('INI: Apalabro->searchWordsExpression');

        $valid = preg_quote(implode('', array_keys($this->points)), '/');
        $expression = str_replace(array('*', '/'), '', mb_strtolower($expression));
        $expression_tiles = $this->splitWord(preg_replace('/[^'.$valid.']/', '', $expression));

        if ($expression_tiles) {
            $tiles = array_merge($tiles, $expression_tiles);
        }

        $wildcards = count(array_keys($tiles, '*'));
        $words = array();

        foreach ($this->dictionary as $word) {
            if (!preg_match('/'.$expression.'/', $word)) {
                continue;
            }

            if (!$this->stringInArray($word, $tiles, $wildcards)) {
                continue;
            }

            $points = $this->getWordPoints($word, $tiles);

            if (!isset($words[$points])) {
                $words[$points] = array($word);
            } else if (!in_array($word, $words[$points])) {
                $words[$points][] = $word;
            }
        }

        foreach ($words as $points => $list) {
            $words[$points] = array_slice($list, 0, 20);
        }

        krsort($words);

        $this->Timer->mark('END: Apalabro->searchWordsExpression');

        return $words;
    }

    public function getWordPoints ($word, $compare = array())
    {
        $this->_loggedOrDie();

        if (!is_array($word)) {
            $word = $this->splitWord($word);
        }

        $full = count($compare);
        $used = count(array_search('*', $word));

        $points = 0;

        foreach ($word as $letter) {
            if ($compare) {
                if (($key = array_search($letter, $compare)) === false) {
                    continue;
                }

                unset($compare[$key]);

                if (isset($this->points[$letter])) {
                    $points += $this->points[$letter];
                    ++$used;
                }
            } else if (isset($this->points[$letter])) {
                $points += $this->points[$letter];
                ++$used;
            }
        }

        if (($full === 7) && ($used >= 7) && (empty($compare) || (isset($compare[0]) && ($compare[0] === '*')))) {
            $points += 40;
        }

        return $points;
    }

    public function getPlayPoints ($tiles)
    {
        $this->_loggedOrDie();

        $valid = preg_quote(implode('', array_keys($this->points)), '/');

        foreach ($tiles as $cell => $letter) {
            if (preg_match('/^[0-9]+$/', $cell) && preg_match('/^['.$valid.'\-]+$/', $letter)) {
                $tiles[$cell] = str_replace('-', '*', mb_strtolower($letter));
            } else {
                unset($tiles[$cell]);
            }
        }

        if (empty($tiles)) {
            return array();
        }

        $this->loadBoardSpaces($tiles);

        $matched = $this->Game->board_spaces['added'];

        if (empty($matched)) {
            return array();
        }

        $words = array();

        foreach ($matched as $word) {
            $words[] = implode('', $word['letters']);
        }

        $words = array_unique($words);

        $check = $this->Curl->post('dictionaries/'.mb_strtoupper($this->language), mb_strtoupper(implode(',', $words)));

        if (empty($check)) {
            return false;
        }

        $this->loadBoardPoints();

        array_walk($check->ok, function (&$value) {
            $value = mb_strtolower($value);
        });

        foreach ($matched as &$word) {
            if (!in_array(mb_strtolower(implode('', $word['letters'])), $check->ok)) {
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

    public function loadBoardSpaces ($added = array())
    {
        $this->_loggedOrDie();

        $Game = &$this->Game;

        if (isset($Game->board_spaces)) {
            return $Game->board_spaces;
        }

        $Game->board_spaces = array();

        if (empty($Game->board_tiles) && empty($added)) {
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
        $this->loadLetters();
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

    private function loadLetters ()
    {
        if ($this->points) {
            return true;
        }

        $this->points = array();
        $this->quantity = array();

        $file = BASE_PATH.'/languages/'.$this->language.'/points.php';

        if (!is_file($file)) {
            return false;
        }

        $letters = include ($file);

        foreach ($letters as $letter => $values) {
            $this->points[$letter] = $values['points'];
            $this->quantity[$letter] = $values['quantity'];
        }

        uksort($this->points, function($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });
    }

    public function mergeDic ($language, $file, $new)
    {
        if (!is_file($file)) {
            return false;
        }

        $this->language = mb_strtolower($language);

        $this->loadLanguage();

        if (empty($this->points)) {
            return false;
        }

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

        $current = encode2utf('àáâãäçèéêëìíîïòóôõöùúûüñýÿ');
        $replacement = 'aaaaaceeeeiiiiooooouuuunyy';
        $valid = array_keys($this->points);

        foreach ($valid as $letter) {
            if (($position = mb_strpos($current, $letter)) !== false) {
                $current = mb_substr($current, 0, $position).mb_substr($current, $position + mb_strlen($letter));
                $replacement = mb_substr($replacement, 0, $position).mb_substr($replacement, $position + mb_strlen($letter));
            }
        }

        $dictionary = encode2utf(file_get_contents($file));
        $dictionary = trim(mb_strtolower(str_replace(chr(0), '', $dictionary)));
        $dictionary = mb_strtr($dictionary, $current, $replacement);
        $dictionary = str_replace(array("\r\n", "\r", "\n"), array("\n", "\n", ' '), $dictionary);
        $dictionary = array_unique(explode(' ', trim(preg_replace('/\s+/', ' ', $dictionary))));

        $valid = preg_quote(implode('', $valid), '/');

        foreach ($dictionary as $key => $word) {
            if (!preg_match('/^['.$valid.']{2,17}$/', $word)) {
                unset($dictionary[$key]);
            }
        }

        $dictionary = array_filter($dictionary);

        sort($dictionary);

        return $dictionary;
    }

    private function stringInArray ($string, $array, $wildcards)
    {
        if (empty($string)) {
            return false;
        }

        if ($array) {
            foreach ($array as $letter) {
                if (empty($letter) || (($position = mb_strpos($string, $letter)) === false)) {
                    continue;
                }

                $string = mb_substr($string, 0, $position).mb_substr($string, $position + mb_strlen($letter));

                if (empty($string)) {
                    return true;
                }
            }
        }

        if (empty($wildcards)) {
            return false;
        }

        foreach (array_keys($this->points) as $letter) {
            while (($position = mb_strpos($string, $letter)) !== false) {
                $string = mb_substr($string, 0, $position).mb_substr($string, $position + mb_strlen($letter));

                --$wildcards;

                if ($wildcards < 0) {
                    return false;
                } else if (empty($string)) {
                    return true;
                }
            }
        }

        return true;
    }

    private function splitWord ($word)
    {
        if (is_string($word)) {
            $words = array();

            foreach (array_keys($this->points) as $letter) {
                while (($position = mb_strpos($word, $letter)) !== false) {
                    $words[] = mb_substr($word, $position, mb_strlen($letter));
                    $word = mb_substr($word, 0, $position).mb_substr($word, $position + mb_strlen($letter));
                }
            }
        } else {
            $words = $word;
        }

        return $words;
    }
}
