<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1>
        <a href="<?php echo BASE_WWW; ?>">&laquo;</a>

        <?php echo $Game->opponent->name; ?>

        <small><?php
            __e($Game->game_status);

            if ($Game->game_status === 'ACTIVE') {
                echo ' ('.($Game->my_turn ? __('Your turn') : __('Opponent turn')).')';
            } else if (isset($Game->last_turn->type) && ($Game->last_turn->type !== 'PLACE_TILE')) {
                echo ' ('.__($Game->last_turn->type).')';
            }
        ?></small>

        <p>
            <small class="label"><?php echo humanDate($Game->last_turn->play_date); ?></small>

            <small class="label label-<?php echo ($Game->my_score > $Game->opponent_score) ? 'success' : 'important'; ?>"><?php
                echo $Game->my_score.' / '.$Game->opponent_score;
            ?></small>

            <?php if (isset($Game->last_turn->words)) { ?>
            <small class="label label-info"><?php __e('Last words: %s', str_replace('-', ', ', $Game->last_turn->words)); ?></small>
            <?php } ?>

            <small class="label label-info"><?php __e('%s tiles to remaining', $Game->remaining_tiles); ?></small>
        </p>
    </h1>
</div>

<div class="row">
    <form id="game-form" action="?id=<?php echo $Game->id; ?>" method="post" class="form-horizontal">
        <?php if (in_array($Game->game_status, array('ACTIVE', 'PENDING_FIRST_MOVE')) && $Game->my_turn) { ?>
        <input type="hidden" name="play" value="true" />
        <?php } ?>

        <div class="span7 relative">
            <table class="board">
                <?php echo $Api->getBoard($Game->id); ?>
            </table>

            <div class="rack-tiles" height="50">
                <?php foreach ($Game->my_rack_tiles as $tile) { ?>
                <div class="tile-35<?php echo (strstr($tile, '*') === false) ? '' : ' wildcard'; ?>">
                    <span class="letter"><?php echo $tile; ?></span>
                    <span class="points"><?php echo $Api->getWordPoints($tile); ?></span>
                </div>
                <?php } ?>
            </div>

            <?php if (in_array($Game->game_status, array('ACTIVE', 'PENDING_FIRST_MOVE')) && $Game->my_turn) { ?>
            <fieldset class="form-actions">
                <button type="submit" name="play" value="true" class="btn btn-primary" disabled="disabled"><?php __e('Play!'); ?></button>
            </fieldset>
            <?php } ?>
        </div>

        <?php if ($words) { ?>
        <div class="span3">
            <h3><?php __e('Suggested words'); ?></h3>

            <!--input type="text" class="filter-words" data-list=".words-list" value="" /-->

            <dl class="dl-horizontal max-height-500 words-list">
                <?php foreach ($words as $points => $words) { ?>
                <dt><?php __e('%s points', $points); ?></dt>
                <dd><?php echo implode('</dd><dd>', $words); ?></dd>
                <?php } ?>
            </dl>
        </div>
        <?php } ?>
    </form>
</div>
