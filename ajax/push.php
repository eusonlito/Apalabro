<?php
if (!$_POST['u']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$updated = json_decode(base64_decode($_POST['u']));

if (!is_object($updated)) {
    die();
}

$games = $Api->getGames('all');
$message = array();

foreach ($games as $Game) {
    if (!isset($Game->last_turn->play_date)) {
        continue;
    }

    if (!isset($updated->{$Game->id})) {
        $message[] = array(
            'id' => $Game->id,
            'text' => __('%s wants to play with you', $Game->opponent->name),
            'link' => (BASE_WWW.'game.php?id='.$Game->id)
        );
    } else if ($Game->last_turn->play_date !== $updated->{$Game->id}) {
        $message[] = array(
            'id' => $Game->id,
            'text' => __('%s has updated the game', $Game->opponent->name),
            'link' => (BASE_WWW.'game.php?id='.$Game->id)
        );
    }
}

die(json_encode($message));
