<?php
namespace Lito\Apalabro;

ini_set('memory_limit', -1);

mb_internal_encoding('UTF-8');

define('DOCUMENT_ROOT', preg_replace('#[/\\\]+#', '/', realpath(getenv('DOCUMENT_ROOT'))));
define('BASE_PATH', preg_replace('#[/\\\]+#', '/', realpath(__DIR__.'/../../../').'/'));
define('BASE_WWW', preg_replace('|^'.DOCUMENT_ROOT.'|i', '', BASE_PATH));
define('FILENAME', basename(getenv('SCRIPT_FILENAME')));

use Lito\Timer\Timer;
use Lito\Cookie\Cookie;
use Lito\Gettext\Gettext;
use Lito\Curl\Curl;

require (__DIR__.'/functions.php');
require (__DIR__.'/Autoload.php');

Autoload::register();
Autoload::registerComposer();

$Timer = new Timer;
$Debug = new Debug;
$Cache = new Cache;
$Cookie = new Cookie;

$Cookie->setName('apalabro');

$Gettext = new Gettext;

$Gettext->setPath(BASE_PATH.'languages');
$Gettext->setCookie($Cookie);
$Gettext->init();

$Curl = new Curl;

$Curl->setTimer($Timer);
$Curl->setDebug($Debug);
$Curl->setCache($Cache);

$Api = new Apalabro();

$Api->setTimer($Timer);
$Api->setDebug($Debug);
$Api->setCache($Cache);
$Api->setCookie($Cookie);
$Api->setCurl($Curl);

if (isset($_GET['language'])) {
    $Gettext->setLanguage($_GET['language'], true);
}

if (isset($_GET['logout'])) {
    $Api->logout();

    header('Location: '.BASE_WWW);
    exit;
}

$Theme = new Theme();

define('BASE_THEME', $Theme->www());
