<?php
namespace Lito\Apalabro;

ini_set('memory_limit', -1);

mb_internal_encoding('UTF-8');

define('DOCUMENT_ROOT', preg_replace('#[/\\\]+#', '/', realpath(getenv('DOCUMENT_ROOT'))));
define('BASE_PATH', preg_replace('#[/\\\]+#', '/', realpath(__DIR__.'/../../../').'/'));
define('BASE_WWW', preg_replace('|^'.DOCUMENT_ROOT.'|i', '', BASE_PATH));
define('FILENAME', basename(getenv('SCRIPT_FILENAME')));

use ANS\Timer\Timer;
use ANS\Cookie\Cookie;
use ANS\Gettext\Gettext;
use ANS\Curl\Curl;
use ANS\Cache\Cache;

require (__DIR__.'/functions.php');
require (__DIR__.'/Autoload.php');

Autoload::register();

foreach (glob(BASE_PATH.'libs/ANS/*', GLOB_ONLYDIR) as $app) {
    Autoload::registerNamespace('ANS\\'.basename($app), $app.'/libs/ANS/'.basename($app).'/');
}

$Timer = new Timer;
$Debug = new Debug;
$Cookie = new Cookie;

$Cookie->setSettings(array(
    'name' => 'apalabro2',
    'path' => BASE_WWW
));

$Cache = new Cache;

$Cache->setSettings(array(
    'interface' => 'files',
    'expire' => (3600 * 24 * 30),
    'folder' => (BASE_PATH.'cache/'),
    'compress' => true,
    'chunk' => 4
));

$Gettext = new Gettext;

$Gettext->setPath(BASE_PATH.'languages');
$Gettext->setCookie($Cookie, 'language');
$Gettext->setDefaultLanguage('en');
$Gettext->init();

$Curl = new Curl;

$Curl->setTimer($Timer);
$Curl->setDebug($Debug, 'showIf');
$Curl->setCache($Cache);

$Api = new Apalabro();

$Api->setTimer($Timer);
$Api->setDebug($Debug, 'showIf');
$Api->setCache($Cache);
$Api->setCookie($Cookie);
$Api->setCurl($Curl);

if (isset($_GET['language'])) {
    $Gettext->setLanguage($_GET['language'], true);
}

if (isset($_GET['logout'])) {
    $Api->logout();

    if (isAjax()) {
        dieJson(array(
            'error' => true,
            'html' => __('You must be logged to view this section')
        ));
    } else {
        die(header('Location: '.BASE_WWW));
    }
}

$Theme = new Theme();

define('BASE_THEME', $Theme->www());
