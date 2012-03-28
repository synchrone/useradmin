<div class="block">

    <h1><?php echo __('reset.password'); ?></h1>

    <div class="content">
        <?php echo Form::open('user/reset'); ?>

        <ul>
           <li>
              <label><?php echo __('account.email.address'); ?>:</label>
              <?php echo Form::input('reset_email', '', array('class' => 'text')) ?>
           </li>
           <li>
              <label><?php echo __('password.reset.token'); ?>:</label>
              <?php echo Form::input('reset_token', '', array('class' => 'text')) ?>
           </li>
        </ul>

        <br style="clear:both;">

        <?php echo Form::submit(NULL, __('reset.password')); ?>
        <?php echo Form::close() ?>
    </div>

</div>
