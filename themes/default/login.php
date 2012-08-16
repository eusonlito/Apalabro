<?php defined('BASE_PATH') or die(); ?>

<div class="row">
    <div class="span7">
        <table class="board"></table>
    </div>

    <div class="span5">
        <div class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-login" data-toggle="tab"><?php __e('Login'); ?></a></li>
                <li><a href="#tab-register" data-toggle="tab"><?php __e('Register'); ?></a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="tab-login">
                    <form action="<?php echo getenv('REQUEST_URI'); ?>" method="post" class="login-form well">
                        <fieldset>
                            <div class="hide">
                                <input type="text" name="email" value="" class="required" />
                            </div>

                            <label for="user"><?php __e('Your email'); ?></label>
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

                <div class="tab-pane" id="tab-register">
                    <?php __e('register_info'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
