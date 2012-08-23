<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

if (isset($_POST['new']) && isset($_POST['language'])) {
    $user = ($_POST['new'] === 'random') ? 0 : $_POST['user_id'];

    $Game = $Api->newGame($_POST['language'], $user);

    if (isset($Game->id)) {
        header('Location: '.BASE_WWW.'game.php?id='.$Game->id);
        exit;
    }

    $Theme->setMessage(__('Sorry but you can\'t play now. You was added to a waiting list.'), 'error');
}

$friends = $Api->getFriends();

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('New Game'));

include ($Theme->get('base.php'));
