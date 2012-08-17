<?php
if (!isset($_GET['id']) || !$_GET['id']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

$game = $_GET['id'];

require (BASE_PATH.'/aux/game-check.php');

$chat = $Api->getChat();

if ($chat) {
    foreach ($chat as $Message) {
        echo '<div class="row-fluid">';
        echo '<div class="span12">';

        if ($Api->myUser($Message->user_id)) {
            echo '<span class="alert alert-info pull-right">';
        } else {
            echo '<span class="alert alert-success pull-left">';
        }

        echo $Message->message;

        echo '</span></div></div>';
    }

    $Api->resetChat();
} else {
    echo '<h3>'.__('There aren\'t any conversation in this game').'</h3>';
}

include ($Theme->get('sub-timer.php'));

exit;
