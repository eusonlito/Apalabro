<?php
namespace Lito\Apalabro;

define('DOCUMENT_ROOT', preg_replace('#[/\\\]+#', '/', realpath(getenv('DOCUMENT_ROOT'))));
define('BASE_PATH', preg_replace('#[/\\\]+#', '/', realpath(__DIR__.'/../../../').'/'));
define('BASE_WWW', preg_replace('|^'.DOCUMENT_ROOT.'|i', '', BASE_PATH));

require (__DIR__.'/Autoload.php');

Autoload::register();
Autoload::registerComposer();

$Api = new \Lito\Apalabro\Apalabro();

if (isset($_GET['logout'])) {
    $Api->logout();

    header('Location: '.BASE_WWW);
    exit;
}

$Theme = new \Lito\Apalabro\Theme();

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
        header('Location: '.BASE_WWW);
        exit;
    } else {
        $Theme->set('body', 'login.php');

        include ($Theme->get('base.php'));

        exit;
    }
}
