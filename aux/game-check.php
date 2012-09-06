<?php
defined('BASE_PATH') or die();

if (!preg_match('/^[0-9]+$/', $game)) {
    $message = __('No game ID was received');

    if (isAjax()) {
        dieJson(array(
            'error' => true,
            'html' => $message
        ));
    }

    $Theme->meta('title', __('Ops..'));

    $Theme->setMessage($message, 'error', true);

    include ($Theme->get('base.php'));

    die();
}

$Game = $Api->preloadGame($game);

if (!$Game) {
    $message = __('Some error occours triying to load this game. Please reload this page to try it again.');

    if (isAjax()) {
        dieJson(array(
            'error' => true,
            'html' => $message
        ));
    }

    $Theme->meta('title', __('Ops..'));

    $Theme->setMessage($message, 'error', true);

    include ($Theme->get('base.php'));

    die();
}
