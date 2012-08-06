<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1><?php __e('New Game'); ?></h1>
</div>

<form action="<?php echo BASE_WWW; ?>new.php" class="form-horizontal" method="post">
    <input type="hidden" name="new" value="true" />

    <div class="control-group">
        <div class="controls">
            <?php foreach ($Api->getLanguages() as $language) { ?>
            <label for="game-language-<?php echo $language; ?>" class="checkbox inline">
                <input type="radio" id="game-language-<?php echo $language; ?>" name="language" value="<?php echo $language; ?>" />
                <img src="<?php echo BASE_WWW.'languages/'.$language; ?>/flag.png" alt="<?php echo __('language_'.$language); ?>" />
            </label>
            <?php } ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-success"><?php __e('Start a new game with random opponent'); ?></button>
    </div>
</form>
