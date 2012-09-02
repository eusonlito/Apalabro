<?php
if (!isset($_POST['u'])) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$Current = json_decode(base64_decode($_POST['u']));

if (!is_object($Current)) {
    dieJson();
}

$games = $Api->getGames('all');
$message = array();

foreach ($games as $Game) {
    if (!isset($Game->last_turn->play_date)) {
        continue;
    }

    $text = getLastTurnMessage($Game, $Current);

    if (is_string($text)) {
        $message[] = array(
            'id' => $Game->id,
            'key' => md5($Game->id.'|game'),
            'text' => $text,
            'link' => (BASE_WWW.'game.php?id='.$Game->id),
            'type' => 'game'
        );
    }

    if ($Game->my_message_alerts > 0) {
        $message[] = array(
            'id' => $Game->id,
            'key' => md5($Game->id.'|message'),
            'text' => __('%s has sent a new message', $Game->opponent->name),
            'link' => (BASE_WWW.'game.php?id='.$Game->id),
            'type' => 'message'
        );
    }
}

dieJson($message);
