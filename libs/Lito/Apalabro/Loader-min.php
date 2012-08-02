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
