<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$user = isset($_GET['id']) ? $_GET['id'] : 0;

if (!preg_match('/^[0-9]+$/', $user)) {
    $Theme->setMessage(__('No user ID was received'), 'error', true);

    $Theme->meta('title', __('Ops..'));

    include ($Theme->get('base.php'));

    die();
}

$User = $Api->getUser($user);

if (!isset($User->id)) {
    $Theme->setMessage(__('This user does not exists or could not be loaded. Please reload this page to try it again.'), 'error', true);

    $Theme->meta('title', __('Ops..'));

    include ($Theme->get('base.php'));

    die();
}

if (isset($_POST['friend']) && isset($_GET['id']) && !$Api->myUser($_GET['id'])) {
    if ($_POST['friend'] === 'add') {
        $Api->addFriend($User->id);

        $User->is_favorite = true;

        $Theme->setMessage(__('%s was added as friend.', $User->name), 'success');
    } else if ($_POST['friend'] === 'remove') {
        $Api->removeFriend($User->id);

        $User->is_favorite = false;

        $Theme->setMessage(__('%s was removed as friend.', $User->name), 'success');
    }
}

$languages = $Api->getLanguages();

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('%s Profile', $User->name));

include ($Theme->get('base.php'));
