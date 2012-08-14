<?php defined('BASE_PATH') or die(); ?>

<div class="page-header">
    <h1><?php __e('New Game'); ?></h1>
</div>

<form id="new-game" action="<?php echo BASE_WWW; ?>new.php" class="form-horizontal" method="post">
    <input type="hidden" name="new" value="true" />
    <input type="hidden" name="user_id" value="" />

    <div class="row">
        <?php foreach ($Api->getLanguages() as $language) { ?>
        <label for="game-language-<?php echo $language; ?>" class="span1 center">
            <img src="<?php echo BASE_WWW.'languages/'.$language; ?>/flag.png" alt="<?php echo __('language_'.$language); ?>" title="<?php echo __('language_'.$language); ?>" />
            <p><input type="radio" id="game-language-<?php echo $language; ?>" name="language" value="<?php echo $language; ?>" /></p>
        </label>
        <?php } ?>
    </div>

    <div class="tabbable">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-random" data-toggle="tab"><?php __e('Random opponent'); ?></a></li>
            <li><a href="#tab-friends" data-toggle="tab"><?php __e('Recent and Friends'); ?></a></li>
            <li><a href="#tab-search" data-toggle="tab"><?php __e('Search'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab-random">
                <div class="form-actions">
                    <button type="submit" class="btn btn-success"><?php __e('Start a new game with random opponent'); ?></button>
                </div>
            </div>

            <div class="tab-pane" id="tab-friends">
                <?php if ($friends) { ?>

                <div class="row">
                    <?php include ($Theme->get('sub-friends-list.php')); ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success" disabled="disabled"><?php __e('Start a new game'); ?></button>
                </div>

                <?php } else { ?>

                <div class="alert alert-warning"><?php __e('You haven\'t any user in this list'); ?></div>

                <?php } ?>
            </div>

            <div class="tab-pane filter-users" id="tab-search">
                <div class="form-actions">
                    <input type="text" name="search" value="" class="input-large search-query" placeholder="<?php __e('Search...'); ?>" />

                    <button type="submit" class="btn btn-info" data-filtered="#users-result" data-url="<?php echo BASE_WWW; ?>ajax/search-users.php"><?php __e('Search'); ?></button>
                </div>

                <div class="row" id="users-result"></div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success" disabled="disabled"><?php __e('Start a new game'); ?></button>
                </div>
            </div>
        </div>
    </div>
</form>
