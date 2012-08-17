<?php
defined('BASE_PATH') or die();

$allgames = $Api->getGames();
?>

<script type="text/javascript">UPDATED = '<?php echo md5(serialize(getPlayDates($allgames))); ?>';</script>

<div class="home">
    <?php
    foreach (array('turn', 'waiting', 'pending', 'ended') as $status) {
        $games = $allgames[$status];

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
