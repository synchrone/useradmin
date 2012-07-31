
<div class="block">
   <h1><?php echo __('register'); ?></h1>
   <div class="content">
      <div class="info" style="width: 415px;"><small><?php echo __('?already.have.account').' '.Html::anchor('user/login', __('login.here')); ?></small></div>
      <br>
      <p><?php echo __('enter.info.to.register'); ?></p>

<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($defaults)) {
   $form->defaults = $defaults;
} else {
   unset($_POST['password']);
   unset($_POST['password_confirmation']);
   $form->defaults = $_POST;
}
echo $form->open('user/register');
?>

<ul>

   <?php echo View::factory('user/user_edit_form')->set(array('form' => $form)); ?>
   
   <?php if(isset($captcha_enabled) && $captcha_enabled) { ?>
   <li>
       <?php echo $recaptcha_html; ?>
       <br/>
   </li>
   <?php } ?>
   
   <li><?php echo $form->submit(NULL, __('register.account')); ?></li>
</ul>
<br style="clear:both;">
<?php
echo $form->close();
?>
   </div>
</div>