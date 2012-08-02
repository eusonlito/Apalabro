<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

if (!isset($_GET['id'])) {
    $Theme->set('body', 'sub-message.php');

    $Theme->setMessage('No game ID was received', 'error');

    include ($Theme->get('base.php'));

    die();
}

$Game = $Api->getGame($_GET['id']);

if (!$Game) {
    $Theme->set('body', 'sub-message.php');

    $Theme->setMessage('This game does not exists', 'error');

    include ($Theme->get('base.php'));

    die();
}

$Api->setLanguage($Game->language);

$words = $Api->solve($Game->id);

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
