<?php defined('BASE_PATH') or die(); ?>

<?php foreach ($users as $User) { ?>
<div class="span4">
    <div class="well user" data-id="<?php echo $User->id; ?>">
        <div class="row-fluid">
            <div class="span3">
                <?php if ($User->avatar) { ?>

                <img src="<?php echo $User->avatar; ?>" width="50" height="50" />

                <?php } else { ?>

                <div class="tile-50">
                    <span class="letter"><?php echo substr($User->name, 0, 1); ?></span>
                    <span class="points"><?php echo $Api->getWordPoints(substr($User->name, 0, 1)); ?></span>
                </div>

                <?php } ?>
            </div>

            <div class="span9">
                <h4><?php echo $User->name; ?></h4>

                <small class="label"><?php echo __('last login %s', humanDate($User->last_log)); ?></small>
            </div>
        </div>
    </div>
</div>
<?php } ?>
