<li><label><?php echo __('username'); ?></label></li>
<?php echo $form->input('username', null, array('info' => __('allowed.usernames'))); ?>
<li><label><?php echo __('email.address'); ?></label></li>
<?php echo $form->input('email') ?>
<li><label><?php echo __('password'); ?></label></li>
<?php echo $form->password('password', null, array('info' => __('allowed.passwords'))) ?>
<li><label><?php echo __('retype.password'); ?></label></li>
<?php echo $form->password('password_confirm') ?>