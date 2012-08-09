<?php

if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['played_tiles']) || !$_POST['played_tiles']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

$game = $_POST['id'];

require (BASE_PATH.'/aux/game-check.php');

$words = $Api->getPlayPoints($Game->id, $_POST['played_tiles']);

$html = '';
$error = false;


if ($words) {
    $total = 0;

    $html = '<table class="table table-condensed">';
    $html .= '<head><tr>';
    $html .= '<th>'.__('Word').'</th><th>'.__('Points').'</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($words as $word) {
        if (!isset($word['ok'])) {
            $error = true;
        }

        $html .= '<tr class="alert alert-'.(isset($word['ok']) ? 'success' : 'danger').'">';
        $html .= '<td>'.implode('', $word['letters']).'</td>';

        if (isset($word['ok'])) {
            $points = array_sum($word['points']);
            $total += $points;

            $html .= '<td>'.$points.'</td>';
        } else {
            $html .= '<td>'.__('No valid word').'</td>';
        }

        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '<tfoot><tr>';
    $html .= '<th>&nbsp;</th><th>'.__('Total %s points', $total).'</th>';
    $html .= '</tr></tfoot></table>';
} else {
    $error = true;
    $html = '<div class="alert alert-danger"><p>'.__('Sorry but these tiles can not be combined. Please, try it another cells').'</p></div>';
}

die(json_encode(array('error' => $error, 'html' => $html)));
