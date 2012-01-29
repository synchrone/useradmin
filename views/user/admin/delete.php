<div class="block">

    <h1><?php echo __('?delete.user'); ?></h1>

    <div class="content">
    <?php

    echo Form::open('admin_user/delete/'.$id, array('style' => 'display: inline;'));

    echo Form::hidden('id', $id);

    echo '<p>'.__('?sure.to.delete.user', array(':user' => $data['username'])).'</p>';

    echo '<p>'.Form::radio('confirmation', 'Y', false, array('id' => 'conf_y')).' <label for="conf_y" style="display: inline;">'.__('yes').'</label><br/>';
    echo Form::radio('confirmation', 'N', true, array('id' => 'conf_n')).' <label for="conf_n" style="display: inline;">'.__('no').'</label><br/></p>';
    echo Form::submit(NULL, __('delete'));
    echo Form::close();

    echo Form::open('admin_user/index', array('style' => 'display: inline; padding-left: 10px;'));
    echo Form::submit(NULL, __('cancel'));
    echo Form::close();
    ?>
   </div>

</div>

