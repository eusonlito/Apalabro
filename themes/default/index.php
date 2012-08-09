<?php defined('BASE_PATH') or die(); ?>

<div class="home">
    <?php
    foreach (array('turn', 'waiting', 'pending', 'ended') as $status) {
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
            echo '<div class="well height-auto">'.__('No games here :)').'</div>';
        }
    }
    ?>
</div>
