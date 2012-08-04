<?php
defined('BASE_PATH') or die();

$message = $Theme->getMessage();

if (!$message) {
    return true;
}
?>

<div class="alert alert-<?php echo $message['status']; ?>">
    <h1>
        <?php if ($message['back']) { ?>
        <a href="<?php echo getenv('HTTP_REFERER'); ?>">&laquo;</a>
        <?php } ?>

        <?php echo $message['text']; ?>
    </h1>
</div>
