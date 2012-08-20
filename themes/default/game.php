<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1>
        <a href="<?php echo BASE_WWW; ?>profile.php?id=<?php echo $Game->opponent->id; ?>"><?php
            echo $Game->opponent->name;
        ?></a>

        <small><?php
            __e($Game->game_status);

            if ($Game->active) {
                echo ' ('.($Game->my_turn ? __('Your turn') : __('Opponent turn')).')';
            } else if (isset($Game->last_turn->type) && ($Game->last_turn->type !== 'PLACE_TILE')) {
                echo ' ('.__($Game->last_turn->type).')';
            }
        ?></small>

        <?php if ((isset($Game->messages) && $Game->messages) || (isset($Game->my_message_alerts) && $Game->my_message_alerts)) { ?>
        <a href="<?php echo BASE_WWW; ?>ajax/get-chat.php?id=<?php echo $Game->id; ?>" class="chat-24" title="<?php __e('You have %s new chat messages', $Game->my_message_alerts); ?>"><?php
            echo (isset($Game->my_message_alerts) && $Game->my_message_alerts) ? $Game->my_message_alerts : '';
        ?></a>
        <?php } ?>

        <p>
            <small class="label"><abbr class="timeago" title="<?php echo timeAgo($Game->last_turn->play_date); ?>"><?php echo humanDate($Game->last_turn->play_date); ?></abbr></small>

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

<?php if ((isset($Game->messages) && $Game->messages) || (isset($Game->my_message_alerts) && $Game->my_message_alerts)) { ?>
<div id="modal-chat" class="modal hide">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3><?php __e('Chat'); ?></h3>
    </div>

    <div class="modal-body max-height"></div>

    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal"><i class="icon-remove"></i> <?php __e('Close'); ?></a>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="span7 relative">
        <form id="game-form" action="?id=<?php echo $Game->id; ?>" method="post" class="form-horizontal">
            <?php if ($Game->active) { ?>
            <input type="hidden" name="id" value="<?php echo $Game->id; ?>" />

            <?php if ($Game->my_turn) { ?>
            <div class="swap hide">
                <h2><?php __e('Swapping Tiles'); ?></h2>

                <div class="page-header">
                    <div class="row">
                        <button type="submit" name="swap" value="true" class="span2 offset1 btn btn-success"><?php __e('Swap'); ?></button>
                        <button class="span2 offset1 btn btn-danger"><?php __e('Cancel'); ?></button>
                    </div>
                </div>

                <div class="well">
                    <h4><?php __e('Move here yours tiles'); ?></h4>

                    <div class="droppable-swap"></div>
                </div>
            </div>
            <?php } ?>

            <?php } ?>

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

            <?php if ($Game->game_status !== 'ENDED') { ?>
            <fieldset class="well">
                <div class="row-fluid">
                    <?php if ($Game->active) { ?>

                    <div class="span3">
                        <?php if ($Game->my_turn) { ?>
                        <a href="#" data-action="confirm" data-url="<?php echo BASE_WWW; ?>ajax/get-points.php" class="btn btn-large btn-success" disabled="disabled">
                            <i class="icon-ok icon-white"></i> <?php __e('Play!'); ?>
                        </a>
                        <?php } else { ?>
                        <a href="#" data-action="test" data-url="<?php echo BASE_WWW; ?>ajax/get-points.php" class="btn btn-large btn-success" disabled="disabled">
                            <i class="icon-ok icon-white"></i>  <?php __e('Test'); ?>
                        </a>
                        <?php } ?>
                    </div>

                    <div class="span9">
                        <div class="pull-right">
                            <a href="#" data-action="recall" class="btn btn-primary"><?php __e('Recall'); ?></a>

                            <?php if ($Game->my_turn) { ?>
                            
                            <a href="#" data-action="swap" class="btn btn-info"><?php __e('Swap Tiles'); ?></a>

                            <button type="submit" name="pass" value="true" class="btn btn-warning"><?php __e('Pass'); ?></button>

                            <?php } ?>

                            <div id="modal-confirm" class="modal hide">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                    <h3><?php __e('Confirm your move'); ?></h3>
                                </div>

                                <div class="modal-body"></div>

                                <div class="modal-footer">
                                    <button type="submit" name="play" value="true" class="btn btn-large btn-primary">
                                        <i class="icon-ok icon-white"></i> <?php __e('Confirm'); ?>
                                    </button>

                                    <a href="#" class="btn" data-dismiss="modal"><i class="icon-remove"></i> <?php __e('Back'); ?></a>
                                </div>
                            </div>

                            <button type="submit" name="resign" value="true" class="btn btn-danger"><?php __e('Resign'); ?></button>
                        </div>
                    </div>

                    <?php } ?>
                </div>
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
                <li class="active"><a href="#tab-suggested-words" data-toggle="tab"><?php __e('Suggestions'); ?></a></li>
                <li><a href="#tab-regular-expression" data-toggle="tab"><?php __e('Search'); ?></a></li>
                <li><a href="#tab-tiles" data-toggle="tab"><?php __e('Tiles'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="tab-suggested-words">
                    <div class="control-group">
                        <input type="text" class="span5 filter-list" data-filtered=".words-list li span.word" value="" placeholder="<?php __e('Filter suggested words'); ?>">
                    </div>

                    <ul class="max-height words-list">
                        <?php if ($words) { ?>
                        <?php foreach ($words as $points => $words) { ?>
                        <li class="row-fluid">
                            <div class="span3"><strong><?php __e('%s points', $points); ?></strong></div>
                            <div class="span7">
                                <?php foreach ($words as $word) { ?>
                                <div>
                                    <span class="word"><?php echo $word; ?></span>
                                    <span class="pull-right small"><?php __e('%s letters', mb_strlen($word)); ?></span>
                                </div>
                                <?php } ?>
                            </div>
                        </li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </div>

                <div class="tab-pane" id="tab-regular-expression">
                    <form action="<?php echo BASE_WWW; ?>ajax/search-words.php" class="form-inline filter-expression" data-filtered=".words-expression" method="post">
                        <input type="hidden" name="id" value="<?php echo $Game->id; ?>" />

                        <input type="text" name="filter" value="" class="input-large search-query" placeholder="<?php __e('Search with regular expression'); ?>">

                        <button type="submit" class="btn btn-info"><?php __e('Search'); ?></button>

                        <a href="http://www.phpf1.com/tutorial/php-regular-expression.html" class="btn btn-success pull-right" target="_blank"><?php __e('Help'); ?></a>
                    </form>

                    <ul class="max-height words-expression">
                        <li></li>
                    </ul>
                </div>

                <div class="tab-pane" id="tab-tiles">
                    <p><?php __e('This tiles are not played yet:'); ?></p>

                    <ul class="max-height center">
                        <?php
                        foreach ($remaining_tiles as $tile => $quantity) {
                            echo '<li><p>'.__('<strong>%s</strong> tiles of letter <strong>%s</strong>', $quantity, $tile).'</p></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>