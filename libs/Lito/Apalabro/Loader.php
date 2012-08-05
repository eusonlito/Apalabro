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
        $language = $Gettext->getLanguage();

        if (is_file(BASE_PATH.'languages/'.$language.'/board.png')) {
            $Api->setLanguage($language);
        } else {
            $Api->setLanguage('es');
        }

        $Theme->set('body', 'login.php');

        include ($Theme->get('base.php'));

        exit;
    }
}
