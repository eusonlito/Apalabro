<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

if (isset($_POST['new']) && isset($_POST['language'])) {
    $Game = $Api->newGame($_POST['language']);

    if (isset($Game->id)) {
        header('Location: '.BASE_WWW.'game.php?id='.$Game->id);
        exit;
    }

    $Theme->setMessage(__('Sorry but you can\'t play now. You was added to a waiting list.'), 'error');
}

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
