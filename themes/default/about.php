<?php defined('BASE_PATH') or die(); ?>

<section>
    <div class="page-header">
        <h1><?php __e('About Apalabro!'); ?></h1>
    </div>

    <div class="lead">
        <p><strong>Apalabro!</strong> is a PHP project developed by <a href="https://github.com/eusonlito">Lito</a> to create an Apalabrados (Angry Words) web interface.</p>
        <p>It's easy to install and requires PHP 5.3.</p>
        <p>Only login using user and password is available (Facebook ins't implemented yet).</p>
        <p>You can get the sorce code at <a href="https://github.com/eusonlito/Apalabro">Github</a>.</p>

        <?php if ($Api->logged()) { ?>
        <p>Also, if you have some suggestion or doubt, you can contact with me at <a href="mailto:lito@eordes.com">lito@eordes.com</a>.</p>
        <?php } ?>
    </div>
</section>

<section>
    <div class="page-header">
        <h1><?php __e('Available Languages'); ?></h1>
    </div>

    <div class="row">
        <?php foreach ($Api->getLanguages() as $language) { ?>
        <img src="<?php echo BASE_WWW.'languages/'.$language; ?>/flag.png" alt="<?php __e('language_'.$language); ?>" title="<?php __e('language_'.$language); ?>" class="span1" />
        <?php } ?>
    </div>
</section>
