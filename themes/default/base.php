<?php defined('BASE_PATH') or die(); ?>

<!DOCTYPE html>

<html lang="<?php $Api->getLanguage(); ?>">
    <head>
        <title>Apalabro</title>

        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />

        <link href="<?php echo $Theme->www(); ?>bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet" />
        <link href="<?php echo $Theme->www(); ?>css/styles.css" type="text/css" rel="stylesheet" />

        <script src="<?php echo $Theme->www(); ?>js/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo $Theme->www(); ?>js/jquery-ui/js/jquery-ui-1.8.22.custom.min.js" type="text/javascript"></script>
        <script src="<?php echo $Theme->www(); ?>js/scripts.js" type="text/javascript"></script>
    </head>

    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="row">
                        <div class="span10">
                            <a class="brand" href="<?php echo BASE_WWW; ?>">Apalabro!</a>

                            <div class="nav-collapse">
                                <ul class="nav">
                                    <li class="active"><a href="<?php echo BASE_WWW; ?>">Home</a></li>
                                    <li><a href="<?php echo BASE_WWW; ?>about.php">About</a></li>

                                    <?php if ($Api->logged()) { ?>
                                    <li><a href="<?php echo BASE_WWW; ?>?logout=true">Logout</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <?php if ($Api->Cache->enabled()) { ?>
                        <div class="span2">
                            <a href="?<?php echo http_build_query(array('reload' => true) + $_GET); ?>" class="btn">Reload Cache</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <?php include ($Theme->get('body')); ?>
        </div>
    </body>
</html>
