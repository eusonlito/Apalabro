<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$game = isset($_GET['id']) ? $_GET['id'] : null;

require (__DIR__.'/game-check.php');

if (isset($_POST['play']) && ($_POST['play'] === 'true')) {
    $success = $Api->play($Game->id, $_POST);

    if ($success) {
        $Theme->setMessage(__('Your tiles were set successfully'), 'success');

        $Api->reload();

        $Game = $Api->getGame($Game->id);
    } else {
        $Theme->setMessage(__('Sorry but these word is no valid'), 'error');
    }
}

if ($Game->game_status !== 'ENDED') {
    $words = $Api->solve($Game->id);
} else {
    $words = array();
}

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
