<?php
/*
if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['tiles']) || !$_POST['tiles']) {
    die();
}
*/
require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

//$game = $_POST['game'];
$game = $_GET['id'];

require (BASE_PATH.'/aux/game-check.php');

$Debug->show($Api->getPlayPoints($Game->id, $_GET['tiles']));
