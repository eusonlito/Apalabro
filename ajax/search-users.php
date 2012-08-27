<?php
if (!isset($_POST['filter']) || !$_POST['filter']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$users = $Api->searchUsers($_POST['filter']);

if ($users) {
    include ($Theme->get('sub-users-list.php'));
} else {
    echo '<h3 class="span12 center">'.__('No users founded...').'</h3>';
}

include ($Theme->get('sub-timer.php'));

exit;
