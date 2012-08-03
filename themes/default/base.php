<?php defined('BASE_PATH') or die(); ?>

<!DOCTYPE html>

<html lang="<?php $Api->getLanguage(); ?>">
    <head>
        <title><?php __e('Apalabro!'); ?></title>

        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

        <link rel="shortcut icon" href="<?php echo BASE_WWW; ?>favicon.ico" />

        <link href="<?php echo $Theme->www(); ?>bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
        <link href="<?php echo $Theme->www(); ?>css/styles.css" type="text/css" rel="stylesheet" />

        <style>
        table.board {
            background: url('languages/<?php echo $Api->getLanguage(); ?>/board.png') no-repeat;
        }
        </style>

        <script src="<?php echo $Theme->www(); ?>js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $Theme->www(); ?>js/jquery-ui/js/jquery-ui-1.8.22.custom.min.js" type="text/javascript"></script>
        <script src="<?php echo $Theme->www(); ?>bootstrap/js/bootstrap-dropdown.js" type="text/javascript"></script>
        <script src="<?php echo $Theme->www(); ?>js/scripts.js" type="text/javascript"></script>
    </head>

    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="<?php echo BASE_WWW; ?>"><?php __e('Apalabro!'); ?></a>

                    <div class="nav-collapse">
                        <ul class="nav">
                            <li<?php echo (basename(getenv('SCRIPT_FILENAME')) == 'index.php') ? ' class="active"' : ''; ?>><a href="<?php echo BASE_WWW; ?>"><?php __e('Home'); ?></a></li>

                            <?php if (basename(getenv('SCRIPT_FILENAME')) == 'game.php') { ?>
                            <li class="active"><a href="<?php echo getenv('REQUEST_URI'); ?>"><?php __e('Game'); ?></a></li>
                            <?php } ?>

                            <li<?php echo (basename(getenv('SCRIPT_FILENAME')) == 'about.php') ? ' class="active"' : ''; ?>><a href="<?php echo BASE_WWW; ?>about.php"><?php __e('About'); ?></a></li>
                        </ul>

                        
                        <ul class="nav pull-right">
                            <?php if ($Api->logged()) { ?>

                            <?php if ($Api->Cache->enabled()) { ?>
                            <li><a href="?<?php echo http_build_query(array('reload' => true) + $_GET); ?>"><?php __e('Reload Cache'); ?></a></li>
                            <?php } ?>

                            <li><a href="<?php echo BASE_WWW; ?>?logout=true"><?php __e('Logout'); ?></a></li>

                            <?php } ?>

                            <li class="divider-vertical"></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php __e('language_'.$Api->getLanguage()); ?> <b class="caret"></b></a>

                                <ul class="dropdown-menu">
                                    <?php foreach ($Api->getLanguages() as $language) { ?>
                                    <li<?php ($language === $Api->getLanguage()) ? ' class="active"' : ''; ?>>
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
            <?php include ($Theme->get('body')); ?>
        </div>
    </body>
</html>
