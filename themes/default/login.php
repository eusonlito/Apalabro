<?php defined('BASE_PATH') or die(); ?>

<div class="row">
    <div class="span7">
        <table class="board"></table>
    </div>

    <div class="span5">
        <form action="<?php echo getenv('REQUEST_URI'); ?>" method="post" class="login-form well">
            <fieldset>
                <label for="user"><?php __e('Your username'); ?></label>
                <input type="text" name="user" value="" class="span4" />

                <label for="password"><?php __e('Your password'); ?></label>
                <input type="password" name="password" value="" class="span4" />

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary pull-right">
                        <?php __e('Login'); ?>
                        <i class="icon-chevron-right icon-white"></i>
                    </button>
              </div>
            </fieldset>
        </form>
    </div>
</div>
