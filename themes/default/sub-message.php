<?php
defined('BASE_PATH') or die();

$message = $Theme->getMessage();

if (!$message) {
    return true;
}
?>

<div class="alert alert-<?php echo $message['status']; ?>">
    <strong>
        <?php if ($message['back'] && !isAjax()) { ?>
        <a href="<?php echo getenv('HTTP_REFERER'); ?>">&laquo;</a>
        <?php } ?>

        <?php echo $message['text']; ?>
    </strong>
</div>
