<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$game = isset($_GET['id']) ? $_GET['id'] : null;

require (__DIR__.'/game-check.php');

if ($Game->game_status !== 'ENDED') {
    if (isset($_POST['play']) && ($_POST['play'] === 'true')) {
        $success = $Api->playGame($Game->id, $_POST);

        if ($success) {
            $Theme->setMessage(__('Your tiles were set successfully'), 'success');

            $Game = $Api->getGame($Game->id);
        } else {
            $Theme->setMessage(__('Sorry but these word is no valid'), 'error');
        }
    }

    if (isset($_POST['swap']) && ($_POST['swap'] === 'true')) {
        $success = $Api->swapTiles($Game->id, $_POST['swapped_tiles']);

        if ($success) {
            $Theme->setMessage(__('Your tiles were swapped successfully'), 'success');

            $Game = $Api->getGame($Game->id);
        } else {
            $Theme->setMessage(__('Sorry but yours tiles can not be swapped'), 'error');
        }
    }

    if (isset($_POST['pass']) && ($_POST['pass'] === 'true')) {
        $success = $Api->passTurn($Game->id);

        if ($success) {
            $Theme->setMessage(__('You have passed'), 'success');

            $Game = $Api->getGame($Game->id);
        } else {
            $Theme->setMessage(__('Sorry but some problem occours when try to pass'), 'error');
        }
    }

    if (isset($_POST['resign']) && ($_POST['resign'] === 'true')) {
        $success = $Api->resignGame($Game->id);

        if ($success) {
            $Theme->setMessage(__('You have resigned this game'), 'success');

            $Game = $Api->getGame($Game->id);
        } else {
            $Theme->setMessage(__('Sorry but some problem occours when try to resign this game'), 'error');
        }
    }

    $words = $Api->solve($Game->id);
} else {
    $words = array();
}

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
