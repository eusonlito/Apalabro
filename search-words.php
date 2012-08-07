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
        echo '<div class="span7">';

        foreach ($words as $word) {
            echo '<span>'.$word.'</span> <span class="pull-right small">'.__('%s letters', mb_strlen($word)).'</span><br />';
        }

        echo '</div>';
        echo '</li>';
    }
}

echo "\n".'<!--';

foreach ($Timer->get() as $timer) {
    echo "\n".sprintf('%01.6f', $timer['total']).' - '.$timer['text'];
}

echo "\n".'//-->';

exit;
