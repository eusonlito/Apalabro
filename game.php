<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

if (!isset($_GET['id'])) {
    $Theme->setMessage(__('No game ID was received'), 'error', true);

    include ($Theme->get('base.php'));

    die();
}

$Game = $Api->getGame($_GET['id']);

if (!$Game) {
    $Theme->setMessage(__('This game does not exists'), 'error', true);

    include ($Theme->get('base.php'));

    die();
}

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

$Api->setLanguage($Game->language);

if ($Game->game_status !== 'ENDED') {
    $words = $Api->solve($Game->my_rack_tiles);
} else {
    $words = array();
}

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
