<?php defined('BASE_PATH') or die(); ?>

<div class="row">
    <?php foreach ($games as $Game) { ?>
    <div class="span4">
        <div class="well">
            <div class="row-fluid">
                <div class="span3">
                    <?php if ($Game->opponent->avatar) { ?>

                    <img src="<?php echo $Game->opponent->avatar; ?>" width="50" height="50" />

                    <?php } else { ?>

                    <div class="tile-50">
                        <span class="letter"><?php echo substr($Game->opponent->name, 0, 1); ?></span>
                        <span class="points"><?php echo $Api->getWordPoints(substr($Game->opponent->name, 0, 1)); ?></span>
                    </div>

                    <?php } ?>
                </div>

                <div class="span9">
                    <h4><a href="<?php echo BASE_WWW; ?>game.php?id=<?php echo $Game->id; ?>"><?php echo $Game->opponent->name; ?></a></h4>

                    <small class="label"><?php echo $Theme->HumaDate($Game->last_turn->play_date); ?></small>

                    <strong class="label label-<?php echo ($Game->my_score > $Game->opponent_score) ? 'success' : 'important'; ?>"><?php
                        echo $Game->my_score.' / '.$Game->opponent_score;
                    ?></strong>

                    <p><small><?php echo $Game->remaining_tiles; ?> tiles to remaining</small></p>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>