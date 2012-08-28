<?php
if (!isset($_GET['id']) || !$_GET['id']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$game = $_GET['id'];

require (BASE_PATH.'/aux/game-check.php');

if ($_POST && isset($_POST['message']) && $_POST['message']) {
    $Api->setChat($_POST['message']);
}

$chat = $Api->getChat();

$html = $chat_id = '';
$new = 0;

if ($chat) {
    $last = isset($_GET['last']) ? $_GET['last'] : '';
    $show = $last ? false : true;

    foreach ($chat as $Message) {
        $chat_id = md5($Message->date);

        if ($last && ($chat_id === $last)) {
            $show = true;
            continue;
        } else if (!$show) {
            continue;
        }

        $html .= '<div class="row-fluid"><div class="span12">';
        $html .= '<span class="alert alert-';
        $html .= $Api->myUser($Message->user_id) ? 'info pull-right' : 'success pull-left';
        $html .= '">'.$Message->message.'</span></div></div>';

        ++$new;
    }

    $Api->resetChat();
} else {
    $html = '<div class="alert alert-empty">'.__('There aren\'t any conversation in this game').'</div>';
}

dieJson(array('id' => $chat_id, 'new' => $new, 'html' => $html));
