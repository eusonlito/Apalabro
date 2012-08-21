<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$game = isset($_GET['id']) ? $_GET['id'] : null;

require (BASE_PATH.'/aux/game-check.php');

$Game = $Api->getGame($Game->id);

if ($Game->game_status !== 'ENDED') {
    if (isset($_POST['play']) && ($_POST['play'] === 'true')) {
        $success = $Api->playGame($_POST);

        if ($success) {
            $Theme->setMessage(__('Your tiles were set successfully'), 'success');

            $Game = $Api->getGame($Game->id, true);
        } else {
            $Theme->setMessage(__('Sorry but these word is no valid'), 'error');
        }
    }

    if (isset($_POST['swap']) && ($_POST['swap'] === 'true')) {
        $success = $Api->swapTiles($_POST['swapped_tiles']);

        if ($success) {
            $Theme->setMessage(__('Your tiles were swapped successfully'), 'success');

            $Game = $Api->getGame($Game->id, true);
        } else {
            $Theme->setMessage(__('Sorry but yours tiles can not be swapped'), 'error');
        }
    }

    if (isset($_POST['pass']) && ($_POST['pass'] === 'true')) {
        $success = $Api->passTurn();

        if ($success) {
            $Theme->setMessage(__('You have passed'), 'success');

            $Game = $Api->getGame($Game->id, true);
        } else {
            $Theme->setMessage(__('Sorry but some problem occours when try to pass'), 'error');
        }
    }

    if (isset($_POST['resign']) && ($_POST['resign'] === 'true')) {
        $success = $Api->resignGame();

        if ($success) {
            $Theme->setMessage(__('You have resigned this game'), 'success');

            $Game = $Api->getGame($Game->id, true);
        } else {
            $Theme->setMessage(__('Sorry but some problem occours when try to resign this game'), 'error');
        }
    }

    $words = $Api->solve();
    $remaining_tiles = $Api->getRemainingTiles();
} else {
    $words = $remaining_tiles = array();
}

$Game->messages = $Api->getChat();

$chat_id = $Game->messages ? md5(end($Game->messages)->date) : '';

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('Game versus %s', $Game->opponent->name));

include ($Theme->get('base.php'));
