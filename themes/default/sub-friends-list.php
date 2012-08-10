<?php defined('BASE_PATH') or die(); ?>

<?php foreach ($friends as $Friend) { ?>
<div class="span4">
    <div class="well user" data-id="<?php echo $Friend->friend->id; ?>">
        <div class="row-fluid">
            <div class="span3">
                <?php if ($Friend->friend->avatar) { ?>

                <img src="<?php echo $Friend->friend->avatar; ?>" width="50" height="50" />

                <?php } else { ?>

                <div class="tile-50">
                    <span class="letter"><?php echo substr($Friend->friend->name, 0, 1); ?></span>
                    <span class="points"><?php echo $Api->getWordPoints(substr($Friend->friend->name, 0, 1)); ?></span>
                </div>

                <?php } ?>
            </div>

            <div class="span9">
                <h4><?php echo $Friend->friend->name; ?></h4>

                <small class="label"><?php echo __('last move %s', humanDate($Friend->lastPlayed)); ?></small>

                <strong class="label label-<?php echo ($Friend->my_wins > $Friend->friend_wins) ? 'success' : (($Friend->my_wins < $Friend->friend_wins) ? 'important' : 'info'); ?>"><?php
                    echo $Friend->my_wins.' / '.$Friend->friend_wins;
                ?></strong>
            </div>
        </div>
    </div>
</div>
<?php } ?>
