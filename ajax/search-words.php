<?php
if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['filter']) || !$_POST['filter']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$game = $_POST['id'];

require (BASE_PATH.'/aux/game-check.php');

$Game = $Api->getGame($Game->id);

$words = $Api->solve($_POST['filter']);

ob_start();

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
} else {
    __e('<div class="alert alert-empty">No results for your query</div>');
}

$html = ob_get_contents();

ob_end_clean();

dieJson(array('html' => $html));
