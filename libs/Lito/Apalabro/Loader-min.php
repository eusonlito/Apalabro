<?php
namespace Lito\Apalabro;

ini_set('memory_limit', -1);

mb_internal_encoding('UTF-8');

define('DOCUMENT_ROOT', preg_replace('#[/\\\]+#', '/', realpath(getenv('DOCUMENT_ROOT'))));
define('BASE_PATH', preg_replace('#[/\\\]+#', '/', realpath(__DIR__.'/../../../').'/'));
define('BASE_WWW', preg_replace('|^'.DOCUMENT_ROOT.'|i', '', BASE_PATH));
define('FILENAME', basename(getenv('SCRIPT_FILENAME')));

require (__DIR__.'/functions.php');
require (__DIR__.'/Autoload.php');

Autoload::register();
Autoload::registerComposer();

$Timer = new Timer();
$Gettext = new Gettext();
$Api = new Apalabro();

if (isset($_GET['language'])) {
    $Gettext->setLanguage($_GET['language'], true);
}

if (isset($_GET['logout'])) {
    $Api->logout();

    header('Location: '.BASE_WWW);
    exit;
}

$Theme = new Theme();
$Debug = new Debug();

define('BASE_THEME', $Theme->www());

$Debug->setDebug(true);
