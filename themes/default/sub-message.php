<?php
defined('BASE_PATH') or die();

$message = $Theme->getMessage();

if (!$message) {
    return true;
}
?>

<div class="alert alert-<?php echo $message['status']; ?>">
    <strong>
        <?php if (!isAjax()) { ?>

        <?php if ($message['back']) { ?>
        <a href="<?php echo getenv('HTTP_REFERER'); ?>">&laquo;</a>
        <?php } else { ?>
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?php } ?>

        <?php } ?>

        <?php echo $message['text']; ?>
    </strong>
</div>
