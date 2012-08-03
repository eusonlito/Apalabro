<?php
defined('BASE_PATH') or die();

foreach (array('pending', 'turn', 'waiting', 'ended') as $status) {
    $games = $Api->getGames($status);

    if (!$games && ($status === 'pending')) {
        continue;
    }

    echo '<div class="page-header">';
    echo '<h1>'.__(ucfirst($status)).'</h1>';
    echo '</div>';

    if ($games) {    
        include ($Theme->get('sub-games-list.php'));
    } else if ($status !== 'pending') {
        echo '<div class="well">'.__('No games here :)').'</div>';
    }
}
