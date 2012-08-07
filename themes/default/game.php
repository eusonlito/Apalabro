<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1>
        <a href="<?php echo BASE_WWW; ?>">&laquo;</a>

        <?php echo $Game->opponent->name; ?>

        <small><?php
            __e($Game->game_status);

            if ($Game->active) {
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

            <small class="label label-info"><?php echo $Game->language; ?></small>

            <?php if (isset($Game->last_turn->words)) { ?>
            <small class="label label-info"><?php __e('Last words: %s', str_replace('-', ', ', $Game->last_turn->words)); ?></small>
            <?php } ?>

            <small class="label label-info"><?php __e('%s tiles to remaining', $Game->remaining_tiles); ?></small>
        </p>
    </h1>
</div>

<div class="row">
    <div class="span7 relative">
        
        <?php if ($Game->active && $Game->my_turn) { ?>
        <div class="swap hide">
            <form id="swap-form" action="?id=<?php echo $Game->id; ?>" method="post" class="form-horizontal">
                <h2><?php __e('Swapping Tiles'); ?></h2>

                <div class="page-header">
                    <div class="row">
                        <button type="submit" name="play" value="swap" class="span2 offset1 btn btn-success"><?php __e('Swap'); ?></button>
                        <button class="span2 offset1 btn btn-danger"><?php __e('Cancel'); ?></button>
                    </div>
                </div>

                <div class="well">
                    <h4><?php __e('Move here yours tiles'); ?></h4>

                    <div class="droppable-swap"></div>
                </div>
            </form>
        </div>
        <?php } ?>

        <table class="board">
            <?php echo $Api->getBoard($Game->id); ?>
        </table>

        <form id="game-form" action="?id=<?php echo $Game->id; ?>" method="post" class="form-horizontal">
            <div class="rack-tiles" height="50">
                <?php foreach ($Game->my_rack_tiles as $tile) { ?>
                <div class="tile-35<?php echo (strstr($tile, '*') === false) ? '' : ' wildcard'; ?>">
                    <span class="letter"><?php echo $tile; ?></span>
                    <span class="points"><?php echo $Api->getWordPoints($tile); ?></span>
                </div>
                <?php } ?>
            </div>

            <?php if ($Game->active && $Game->my_turn) { ?>
            <fieldset class="form-actions">
                <button type="submit" name="play" value="true" class="btn btn-primary" disabled="disabled"><?php __e('Play!'); ?></button>
                <a href="#" name="swap" class="btn btn-primary offset1"><?php __e('Swap Tiles'); ?></a>
            </fieldset>
            <?php } ?>
        </form>
    </div>

    <?php if ($Game->game_status !== 'ENDED') { ?>
    <div class="span5 tabbable">
        <div id="suggestions-previous">
            <h3><?php __e('Do you need help?'); ?></h3>
            <p><?php __e('I can give you some suggestions ;)'); ?></p>
            <button class="span4 btn btn-info"><?php __e('Yes, please!'); ?></button>
        </div>

        <div id="suggestions" class="hide">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab-suggested-words" data-toggle="tab"><?php __e('Suggested words'); ?></a>
                </li>

                <li>
                    <a href="#tab-regular-expression" data-toggle="tab"><?php __e('Regular expression'); ?></a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="tab-suggested-words">
                    <div class="control-group">
                        <input type="text" class="span5 filter-list" data-filtered=".words-list li span" value="" placeholder="<?php __e('Filter suggested words'); ?>">
                    </div>

                    <ul class="dl-horizontal words-list">
                        <?php if ($words) { ?>
                        <?php foreach ($words as $points => $words) { ?>
                        <li class="row-fluid">
                            <div class="span3"><strong><?php __e('%s points', $points); ?></strong></div>
                            <div class="span7">
                                <?php foreach ($words as $word) { ?>
                                <span><?php echo $word; ?></span> <span class="pull-right small"><?php __e('%s letters', mb_strlen($word)); ?></span><br />
                                <?php } ?>
                            </div>
                        </li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </div>

                <div class="tab-pane" id="tab-regular-expression">
                    <form action="<?php echo BASE_WWW; ?>search-words.php" class="form-inline filter-expression" data-filtered=".words-expression" method="post">
                        <input type="hidden" name="game" value="<?php echo $Game->id; ?>" />

                        <input type="text" name="filter" value="" class="input-large search-query" placeholder="<?php __e('Filter with regular expression'); ?>">

                        <button type="submit" class="btn btn-info"><?php __e('Search'); ?></button>

                        <a href="http://www.phpf1.com/tutorial/php-regular-expression.html" class="btn btn-success pull-right" target="_blank"><?php __e('Help'); ?></a>
                    </form>

                    <ul class="dl-horizontal words-expression">
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
