<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1>
        <a href="<?php echo BASE_WWW; ?>">&laquo;</a>

        <?php echo $Game->opponent->name; ?>

        <small><?php
            echo $Game->game_status;

            if (($Game->game_status !== 'ACTIVE') && $Game->remaining_tiles) {
                echo ' ('.$Game->last_turn->type.')';
            }
        ?></small>

        <p>
            <small class="label"><?php echo $Theme->humanDate($Game->last_turn->play_date); ?></small>

            <small class="label label-<?php echo ($Game->my_score > $Game->opponent_score) ? 'success' : 'important'; ?>"><?php
                echo $Game->my_score.' / '.$Game->opponent_score;
            ?></small>

            <small class="label label-info"><?php echo $Game->remaining_tiles; ?> tiles to remaining</small>
        </p>
    </h1>
</div>

<div class="row">
    <div class="span7 tableiro">
        <table class="board">
            <?php echo $Api->getBoard($Game->id); ?>
        </table>

        <div class="rack-tiles">
            <?php foreach ($Game->my_rack_tiles as $tile) { ?>
            <div class="tile-35<?php echo (strstr($tile, '*') === false) ? '' : ' wildcard'; ?>">
                <span class="letter"><?php echo $tile; ?></span>
                <span class="points"><?php echo $Api->getWordPoints($tile); ?></span>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php if ($words) { ?>
    <div class="span3">
        <h3>Suggested words</h3>

        <dl class="dl-horizontal">
            <?php foreach ($words as $points => $words) { ?>
            <dt>With <?php echo $points; ?> points</dt>
            <dd><?php echo implode('</dd><dd>', $words); ?></dd>
            <?php } ?>
        </dl>
    </div>
    <?php } ?>
</div>
