<?php
include (__DIR__.'/Loader-min.php');

if (isset($_GET['reload'])) {
    $Api->reload();
}

if (!$Api->logged()) {
    if (isset($_POST['user']) && isset($_POST['password'])) {
        $logged = $Api->login($_POST['user'], $_POST['password']);
    } else {
        $logged = false;
    }

    if ($logged) {
        header('Location: '.BASE_WWW.'?reload=1');
        exit;
    } else {
        $Theme->set('body', 'login.php');

        include ($Theme->get('base.php'));

        exit;
    }
}
