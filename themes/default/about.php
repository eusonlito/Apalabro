<?php defined('BASE_PATH') or die(); ?>

<section>
    <div class="page-header">
        <h1><?php __e('About Apalabro!'); ?></h1>
    </div>

    <div class="lead">
        <?php __e('about_info'); ?>
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

<?php if ($git) { ?>
<section>
    <div class="page-header">
        <h1><?php __e('Project Updates'); ?></h1>
    </div>

    <div class="row">
        <?php foreach ($git as $column) { ?>
        <dl class="span6">
            <?php foreach ($column as $row) { ?>
            <dt><?php echo $row['date']; ?></dt>
            <dd><?php echo $row['message']; ?></dd>
            <?php } ?>
        </dl>
        <?php } ?>
    </div>
</section>
<?php } ?>
