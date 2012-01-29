<div class="block">
   <h1><?php echo __('confirm.remove.user.account'); ?></h1>
   <div class="content">
<?php

echo Form::open('user/unregister/'.$id, array('style' => 'display: inline;'));

echo Form::hidden('id', $id);

echo '<p>'.__('?sure.to.remove.user.account').'</p>';

echo '<p>'.Form::radio('confirmation', 'Y').' '.__('yes').'<br/>';
echo Form::radio('confirmation', 'N', true).' '.__('no').'<br/></p>';

echo Form::submit(NULL, __('confirm'));
echo Form::close();

echo Form::open('user/profile', array('style' => 'display: inline; padding-left: 10px;'));
echo Form::submit(NULL, __('cancel'));
echo Form::close();
?>
   </div>
</div>