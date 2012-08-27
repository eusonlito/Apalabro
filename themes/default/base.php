<?php defined('BASE_PATH') or die(); ?>

<!DOCTYPE html>

<html lang="<?php $Gettext->getLanguage(); ?>">
    <head>
        <title><?php echo $Theme->meta('title').' - '.__('Apalabro!'); ?></title>

        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

        <meta name="title" content="<?php __e('Apalabro!'); ?>" />
        <meta property="og:title" content="<?php __e('Apalabro!'); ?>" />
        <meta name="description" content="<?php __e('Play to Apalabrados/Angry Words online!'); ?>" />
        <meta property="og:description" content="<?php __e('Play to Apalabrados/Angry Words online!'); ?>" />
        <meta name="image" content="<?php echo BASE_THEME; ?>images/logo.png" />
        <meta property="og:image" content="<?php echo BASE_THEME; ?>images/logo.png" />

        <link rel="image_src" href="<?php echo BASE_THEME; ?>images/logo.png" />
        <link rel="shortcut icon" href="<?php echo BASE_WWW; ?>favicon.ico" />

        <link href="<?php echo BASE_THEME; ?>bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
        <link href="<?php echo BASE_THEME; ?>css/styles.css?1" type="text/css" rel="stylesheet" />

        <?php if ($Api->getLanguage()) { ?>
        <style>
        table.board {
            background: url('languages/<?php echo $Api->getLanguage(); ?>/board.png') no-repeat;
        }
        </style>
        <?php } ?>

        <script src="<?php echo BASE_THEME; ?>js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo BASE_THEME; ?>js/jquery-ui/js/jquery-ui-1.8.22.custom.min.js" type="text/javascript"></script>
        <script src="<?php echo BASE_THEME; ?>js/jquery-ui/js/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
        <script src="<?php echo BASE_THEME; ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo BASE_THEME; ?>js/jquery.timeago.js" type="text/javascript"></script>

        <script src="<?php echo BASE_THEME; ?>js/strings.js.php?lg=<?php echo $Gettext->getLanguage(); ?>" type="text/javascript"></script>
        <script src="<?php echo BASE_THEME; ?>js/jquery.timeago.locale.js.php?lg=<?php echo $Gettext->getLanguage(); ?>" type="text/javascript"></script>

        <script type="text/javascript">
        var BASE_WWW = '<?php echo BASE_WWW; ?>';
        var BASE_THEME = '<?php echo BASE_THEME; ?>';
        var GAME_ID = 0;
        var VALID_LETTERS = new Array();

        <?php if ($Api->logged()) { ?>

        var UPDATED = '<?php echo base64_encode(json_encode(getPlayDates($Api->getGames('all')))); ?>';

        <?php if (isset($Game)) { ?>
        GAME_ID = <?php echo $Game->id; ?>;
        VALID_LETTERS = new Array('<?php echo implode("','", $Api->getValidWords()); ?>');
        <?php } ?>

        <?php } else { ?>

        var UPDATED = '';

        <?php } ?>

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-33869691-1']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
        </script>

        <script src="<?php echo BASE_THEME; ?>js/scripts.js?1" type="text/javascript"></script>
    </head>

    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="<?php echo BASE_WWW; ?>"><?php __e('Apalabro!'); ?></a>

                    <div class="nav-collapse">
                        <ul class="nav pull-left">
                            <li<?php echo (FILENAME === 'index.php') ? ' class="active"' : ''; ?>><a href="<?php echo BASE_WWW; ?>"><?php __e('Home'); ?></a></li>

                            <?php if ($Api->logged()) { ?>

                            <?php if ((FILENAME === 'game.php') && isset($game) && $game) { ?>
                            <li class="active"><a href="?id=<?php echo $game; ?>"><?php __e('Game'); ?></a></li>
                            <?php } ?>

                            <?php if (FILENAME === 'profile.php') { ?>

                            <?php if (!isset($User->id) || $Api->myUser($User->id)) { ?>
                            <li class="active"><a href="<?php echo BASE_WWW; ?>profile.php"><?php __e('My Profile'); ?></a></li>
                            <?php } else { ?>
                            <li class="active"><a href="?id=<?php echo $User->id; ?>"><?php __e('User Profile'); ?></a></li>
                            <?php } ?>

                            <?php } else { ?>
                            <li><a href="<?php echo BASE_WWW; ?>profile.php"><?php __e('My Profile'); ?></a></li>
                            <?php } ?>

                            <li id="updates" class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <span><?php
                                        __e('Your Turn');

                                        $turn = $Api->getGames('turn');

                                        echo $turn ? (' ('.count($turn).')') : '';
                                    ?></span> <b class="caret"></b>
                                </a>

                                <ul class="dropdown-menu">
                                    <?php
                                    if ($turn) {
                                        foreach ($turn as $Turn) {
                                            $text = getLastTurnMessage($Turn, getPlayDates($Api->getGames('all')));

                                            if (is_string($text)) {
                                                echo '<li id="updated-'.$Turn->id.'" class="current">'
                                                    .'<a href="'.BASE_WWW.'game.php?id='.$Turn->id.'">'
                                                    .$text
                                                    .'</a></li>';
                                            }
                                        }
                                    } else {
                                        echo '<li><span>'.__('There aren\'t games waiting for you').'</span></li>';
                                    }
                                    ?>
                                </ul>
                            </li>

                            <?php } ?>

                            <li<?php echo (FILENAME === 'about.php') ? ' class="active"' : ''; ?>><a href="<?php echo BASE_WWW; ?>about.php"><?php __e('About'); ?></a></li>
                        </ul>
                        
                        <ul class="nav pull-right">
                            <?php if ($Api->logged()) { ?>
                            <li<?php echo (FILENAME === 'new.php') ? ' class="active"' : ''; ?>><a href="<?php echo BASE_WWW; ?>new.php"><?php __e('New Game'); ?></a></li>
                            <li><a href="<?php echo BASE_WWW; ?>?logout=true"><?php __e('Logout'); ?></a></li>
                            <?php } ?>

                            <li class="divider-vertical"></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php __e('language_'.$Gettext->getLanguage()); ?> <b class="caret"></b></a>

                                <ul class="dropdown-menu">
                                    <?php foreach ($Gettext->getLanguages() as $language) { ?>
                                    <li<?php ($language === $Gettext->getLanguage()) ? ' class="active"' : ''; ?>>
                                        <a href="?<?php echo http_build_query(array('language' => $language) + $_GET); ?>"><?php __e('language_'.$language); ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <?php
            include ($Theme->get('sub-message.php'));
            include ($Theme->get('body'));
            ?>
        </div>
    </body>
</html>

<?php include ($Theme->get('sub-timer.php')); ?>
