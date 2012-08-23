<?php defined('BASE_PATH') or die(); ?>

<div class="row">
    <div class="span1">
        <?php if ($User->avatar) { ?>

        <img src="<?php echo $User->avatar; ?>" width="50" height="50" />

        <?php } else { ?>

        <div class="tile-50">
            <span class="letter"><?php echo substr($User->name, 0, 1); ?></span>
            <span class="points"><?php echo $Api->getWordPoints(substr($User->name, 0, 1)); ?></span>
        </div>

        <?php } ?>
    </div>

    <div class="span5">
        <div class="page-header">
            <h1>
                <?php
                if (getenv('HTTP_REFERER') && (basename(getenv('HTTP_REFERER')) !== basename(getenv('REQUEST_URI')))) {
                    echo '<a href="'.getenv('HTTP_REFERER').'">&laquo;</a> ';
                }

                echo $User->name;
                ?>
            </h1>

            <?php if (isset($User->status)) { ?>
            <h4><?php echo $User->status->message; ?></h4>
            <?php } ?>
        </div>

        <?php foreach ($User->games_by_language as $Game) { ?>
        <div class="well pull-left">
            <h4>
                <?php
                $language = mb_strtolower($Game->language);

                if (in_array($language, $languages)) {
                    echo '<img src="'.BASE_WWW.'languages/'.$language.'/flag.png" alt="'.__('language_'.$language).'" />';
                } else {
                    echo '['.$Game->language.']';
                }

                echo ' '.$Game->count;
                ?>
            </h4>
        </div>
        <?php } ?>
    </div>

    <div class="span5">
        <?php if (!$Api->myUser($User->id)) { ?>

        <div class="row-fluid">
            <div class="span6">
                <form action="<?php echo getenv('REQUEST_URI'); ?>" method="post" class="pull-left">
                    <fieldset>
                        <?php if ($User->is_favorite) { ?>
                        <button type="submit" name="friend" value="remove" class="btn btn-danger">
                            <i class="icon-minus icon-white"></i>
                            <?php __e('Remove as friend'); ?>
                        </button>
                        <?php } else { ?>
                        <button type="submit" name="friend" value="add" class="btn btn-success">
                            <i class="icon-plus icon-white"></i>
                            <?php __e('Add as friend'); ?>
                        </button>
                        <?php } ?>
                    </fieldset>
                </form>
            </div>

            <div class="span6">
                <a class="btn btn-primary pull-right" data-action="profile-new-game">
                    <i class="icon-play-circle icon-white"></i>
                    <?php __e('Play a versus game'); ?>
                </a>
            </div>
        </div>

        <div id="modal-profile-new-game" class="modal hide">
            <form action="<?php echo BASE_WWW; ?>new.php" method="post">
                <input type="hidden" name="user_id" value="<?php echo $User->id; ?>" />

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3><?php __e('Select a language'); ?></h3>
                </div>

                <div class="modal-body">
                    <?php foreach ($Api->getLanguages() as $language) { ?>
                    <label for="game-language-<?php echo $language; ?>" class="span1 center">
                        <img src="<?php echo BASE_WWW.'languages/'.$language; ?>/flag.png" alt="<?php echo __('language_'.$language); ?>" title="<?php echo __('language_'.$language); ?>" />
                        <p><input type="radio" id="game-language-<?php echo $language; ?>" name="language" value="<?php echo $language; ?>" /></p>
                    </label>
                    <?php } ?>
                </div>

                <div class="modal-footer">
                    <button type="submit" name="new" value="true" class="btn btn-large btn-primary" disabled="disabled">
                        <i class="icon-play-circle icon-white"></i> <?php __e('Play!'); ?>
                    </button>
                </div>
            </form>
        </div>

        <?php if (($User->my_wins + $User->opponent_wins) > 0) { ?>
        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> finished versus games', $User->my_wins + $User->opponent_wins); ?>
        </p>

        <div class="row-fluid">
            <div class="span6">
                <p class="alert alert-success">
                    <?php __e('<strong>%s</strong> you won!', $User->my_wins); ?>
                </p>
            </div>

            <div class="span6">
                <p class="alert alert-danger">
                    <?php __e('<strong>%s</strong> you lost!', $User->opponent_wins); ?>
                </p>
            </div>
        </div>
        <?php } ?>

        <?php } ?>

        <?php if (isset($User->stats->games_played)) { ?>

        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> played games', $User->stats->games_played); ?>
        </p>

        <div class="row-fluid">
            <div class="span4">
                <p class="alert alert-success">
                    <?php __e('<strong>%s</strong> won', $User->stats->games_won); ?>
                </p>
            </div>

            <div class="span4">
                <p class="alert alert-danger">
                    <?php __e('<strong>%s</strong> lost', $User->stats->games_lost); ?>
                </p>
            </div>

            <div class="span4">
                <p class="alert">
                    <?php __e('<strong>%s%%</strong> resign', $User->stats->games_resign); ?>
                </p>
            </div>
        </div>

        <?php if (isset($User->stats->longest_word)) { ?>
        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the longest word', $User->stats->longest_word); ?>
        </p>
        <?php } ?>

        <?php if (isset($User->stats->best_game_points)) { ?>
        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the best game', $User->stats->best_game_points); ?>
        </p>
        <?php } ?>

        <?php if (isset($User->stats->top_play)) { ?>
        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the best play', $User->stats->top_play); ?>
        </p>
        <?php } ?>

        <?php } ?>

        <p class="alert alert-info">
            <?php __e('last login <strong>%s</strong>', humanDate($User->last_log)); ?>
        </p>
    </div>
</div>
