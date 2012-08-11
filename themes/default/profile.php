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

        <form action="<?php echo getenv('REQUEST_URI'); ?>" method="post">
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

        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> finished versus games', $User->my_wins + $User->opponent_wins); ?>
        </p>

        <?php if (($User->my_wins + $User->opponent_wins) > 0) { ?>
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

        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the longest word', $User->stats->longest_word); ?>
        </p>

        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the best game', $User->stats->best_game_points); ?>
        </p>

        <p class="alert alert-info">
            <?php __e('<strong>%s</strong> was the best play', $User->stats->top_play); ?>
        </p>
        <?php } ?>

        <p class="alert alert-info">
            <?php __e('last login <strong>%s</strong>', humanDate($User->last_log)); ?>
        </p>
    </div>
</div>
