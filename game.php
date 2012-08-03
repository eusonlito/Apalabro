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

if (isset($_POST['play']) && ($_POST['play'] === 'true')) {
    $success = $Api->play($Game->id, $_POST);

    if ($success) {
        $Api->reload();

        $Game = $Api->getGame($Game->id);
    }
}

$Api->setLanguage($Game->language);

$words = $Api->solve($Game->id);

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
