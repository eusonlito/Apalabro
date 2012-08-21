<?php
header('Content-Type: application/json');

if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['played_tiles']) || !$_POST['played_tiles']) {
    die();
}

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

$game = $_POST['id'];

require (BASE_PATH.'/aux/game-check.php');

$Game = $Api->getGame($Game->id);

$words = $Api->getPlayPoints($_POST['played_tiles']);

$html = '';
$error = false;

if ($words) {
    $total = 0;

    foreach ($words as $word) {
        if (!isset($word['ok'])) {
            $error = true;
        }

        $html .= '<div class="row-fluid">';
        $html .= '<div class="alert alert-'.(isset($word['ok']) ? 'success' : 'danger').'">';

        if (isset($word['ok'])) {
            $points = array_sum($word['points']);
            $total += $points;

            $html .= '<div class="points span2">'.$points.'</div>';
            $html .= '<div class="span8">'.implode('', $word['letters']).'</div>';
        } else {
            $html .= '<div class="points span2">0</div>';
            $html .= '<div class="span6">'.implode('', $word['letters']).'</div>';
            $html .= '<div class="span4 pull-right">'.__('No valid word').'</div>';
        }

        $html .= '</div>';
        $html .= '</div>';
    }

    if (!$error && (count($words) > 1)) {
        $html .= '<h4 class="alert alert-info">'.__('Playing for %s points', $total).'</h4>';
    }
} else {
    $error = true;
    $html = '<div class="alert alert-danger"><p>'.__('Sorry but these tiles can not be combined. Please, try it another cells').'</p></div>';
}

die(json_encode(array('error' => $error, 'html' => $html)));
