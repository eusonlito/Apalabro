<?php

if (!isset($_POST['game']) || !$_POST['game'] || !isset($_POST['filter']) || !$_POST['filter']) {
    die();
}

require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$game = $_POST['game'];

require (__DIR__.'/game-check.php');

$words = $Api->solve($Game->id, $_POST['filter']);

if ($words) {
    foreach ($words as $points => $words) {
        echo '<li class="row-fluid">';
        echo '<div class="span3"><strong>'.__('%s points', $points).'</strong></div>';
        echo '<div class="span7"><span>'.implode('</span><span><br />', $words).'</span></div>';
        echo '</li>';
    }
}
