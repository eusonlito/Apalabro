<?php
include (__DIR__.'/Loader-min.php');

if (isset($_GET['reload'])) {
    $Api->reload();
}

if ($Api->logged()) {
    return true;
}

if (isAjax()) {
    dieJson(array('html' => __('You must be logged to view this section')));
}

if (isset($_POST['user']) && isset($_POST['password']) && !$_POST['email']) {
    $logged = $Api->login($_POST['user'], $_POST['password']);
} else {
    $logged = false;
}

if ($logged === true) {
    die(header('Location: '.getenv('REQUEST_URI')));
}

$language = $Gettext->getLanguage();

if (is_file(BASE_PATH.'languages/'.$language.'/board.png')) {
    $Api->setLanguage($language);
} else {
    $Api->setLanguage('es');
}

if (isset($logged->message)) {
    $Theme->setMessage($logged->message, 'error');
}

$Theme->set('body', 'login.php');

$Theme->meta('title', __('Login'));

include ($Theme->get('base.php'));

exit;
