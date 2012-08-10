<?php
if (!isset($_POST['filter']) || !$_POST['filter']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

$users = $Api->searchUsers($_POST['filter']);

if ($users) {
    include ($Theme->get('sub-users-list.php'));
} else {
    __e('No results for your query');
}

include ($Theme->get('sub-timer.php'));

exit;
