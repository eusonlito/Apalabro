<?php
if (!isset($_POST['id']) || !$_POST['id'] || !isset($_POST['played_tiles']) || !$_POST['played_tiles']) {
    die();
}

ini_set('max_execution_time', 30);
ini_set('memory_limit', '32M');

require (__DIR__.'/../libs/Lito/Apalabro/Loader.php');

if (!isAjax()) {
    die();
}

$game = $_POST['id'];

require (BASE_PATH.'/aux/game-check.php');

$Game = $Api->getGame($Game->id);

if (!is_object($Game)) {
    dieJson(array(
        'error' => true,
        'html' => __('Some error occours triying to load this game. Please reload this page to try it again.')
    ));
}

$words = $Api->getPlayPoints($_POST['played_tiles']);

if ($words === false) {
    dieJson(array(
        'error' => true,
        'html' => __('Some error occours triying to load the points. Please close this window and try it again.')
    ));
}

$html = '';
$valid = true;
$error = false;

if ($words) {
    $total = 0;

    foreach ($words as $word) {
        if (!isset($word['ok'])) {
            $valid = false;
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

    if ($valid && (count($words) > 1)) {
        $html .= '<h4 class="alert alert-info">'.__('Playing for %s points', $total).'</h4>';
    }
} else {
    $valid = false;
    $error = true;
    $html = __('Sorry but these tiles can not be combined. Please, try it another cells');
}

dieJson(array(
    'error' => $error,
    'valid' => $valid,
    'html' => $html
));
