<?php
defined('BASE_PATH') or die();

$message = $Theme->getMessage();
?>

<div class="alert alert-error">
    <h1>
        <a href="<?php echo getenv('HTTP_REFERER'); ?>">&laquo;</a>
        <?php echo $message['text']; ?>
    </h1>
</div>
