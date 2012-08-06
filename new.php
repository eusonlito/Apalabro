<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

if (isset($_POST['new']) && isset($_POST['language'])) {
    $Game = $Api->newGame($_POST['language']);

    if ($Game) {
        header('Location: '.BASE_WWW.'game.php?id='.$Game->id);
        exit;
    }

    $Theme->setMessage(__('Sorry but I can\'t create a new game... Please try it again in some minutes'), 'error');
}

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
