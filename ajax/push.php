<?php
require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$referer = parse_url(getenv('HTTP_REFERER'));
$file = basename($referer['path']);

switch ($file) {
    case 'game.php':
        parse_str($referer['query'], $query);

        isset($query['id']) or die();

        $game = $query['id'];

        require (BASE_PATH.'/aux/game-check.php');

        die(json_encode(array('text' => $Game->last_turn->play_date)));

    default:
        if (strstr($file, '.php')) {
            die(json_encode(array('error' => true)));
        }

        $games = getPlayDates($Api->getGames());

        if (!$games) {
            die(json_encode(array('error' => true)));
        }

        die(json_encode(array('text' => md5(serialize($games)))));
}

exit;
