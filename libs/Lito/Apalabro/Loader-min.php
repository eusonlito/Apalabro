<?php
namespace Lito\Apalabro;

ini_set('memory_limit', -1);

define('DOCUMENT_ROOT', preg_replace('#[/\\\]+#', '/', realpath(getenv('DOCUMENT_ROOT'))));
define('BASE_PATH', preg_replace('#[/\\\]+#', '/', realpath(__DIR__.'/../../../').'/'));
define('BASE_WWW', preg_replace('|^'.DOCUMENT_ROOT.'|i', '', BASE_PATH));

require (__DIR__.'/functions.php');
require (__DIR__.'/Autoload.php');

Autoload::register();
Autoload::registerComposer();

$Api = new \Lito\Apalabro\Apalabro();

if (isset($_GET['language'])) {
    $Api->setLanguage($_GET['language'], true);
}

if (isset($_GET['logout'])) {
    $Api->logout();

    header('Location: '.BASE_WWW);
    exit;
}

$Theme = new \Lito\Apalabro\Theme();
$Debug = new \Lito\Apalabro\Debug();

$Debug->setDebug(true);
