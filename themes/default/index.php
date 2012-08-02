<?php
defined('BASE_PATH') or die();

foreach (array('turn', 'waiting', 'ended') as $status) {
    echo '<div class="page-header">';
    echo '<h1>'.__(ucfirst($status)).'</h1>';
    echo '</div>';

    if ($games = $Api->getGames($status)) {
        include ($Theme->get('sub-games-list.php'));
    } else {
        echo '<div class="well">'.__('No games here :)').'</div>';
    }
}
