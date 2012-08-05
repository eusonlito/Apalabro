<?php
defined('BASE_PATH') or die();

if (!preg_match('/^[0-9]+$/', $game)) {
    $Theme->setMessage(__('No game ID was received'), 'error', true);

    include ($Theme->get('base.php'));

    die();
}

$Game = $Api->getGame($game);

if (!$Game) {
    $Theme->setMessage(__('This game does not exists'), 'error', true);

    include ($Theme->get('base.php'));

    die();
}
