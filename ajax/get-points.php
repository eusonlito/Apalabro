<?php
require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

$game = isset($_GET['id']) ? $_GET['id'] : null;

require (BASE_PATH.'/game-check.php');

$Debug->show($Api->getBoardSpaces($Game->id));
