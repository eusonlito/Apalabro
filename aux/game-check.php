<?php
defined('BASE_PATH') or die();

if (!preg_match('/^[0-9]+$/', $game)) {
    $Theme->setMessage(__('No game ID was received'), 'error', true);

    $Theme->meta('title', __('Ops..'));

    if (isAjax()) {
        include ($Theme->get('sub-message.php'));
    } else {
        include ($Theme->get('base.php'));
    }

    die();
}

$Game = $Api->preloadGame($game);

if (!$Game) {
    $Theme->setMessage(__('Some error occours triying to load this game. Please reload this page to try it again.'), 'error', true);

    $Theme->meta('title', __('Ops..'));

    if (isAjax()) {
        include ($Theme->get('sub-message.php'));
    } else {
        include ($Theme->get('base.php'));
    }

    die();
}
