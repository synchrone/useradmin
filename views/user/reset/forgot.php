<div class="block">

   <h1><?php echo __('?forgot.password.or.username'); ?></h1>

   <div class="content">
        <p><?php echo __('send.password.resetlink'); ?></p>
        
        <?php
        echo Form::open('user/forgot');
        echo '<p>'.__('email.address').': '.Form::input('reset_email', '', array('class' => 'text')).'</p>';
        ?>

        <?php
        echo Form::submit(NULL, __('reset.password'));
        echo Form::close();
        ?>
   </div>

</div>
