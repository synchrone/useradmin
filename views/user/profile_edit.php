<?php
$form = new Appform();
if(isset($errors)) {
   $form->errors = $errors;
}
if(isset($data)) {
   unset($data['password']);
   $form->values = $data;
}
echo $form->open('user/profile_edit');
?>
<div class="block">
   <h1><?php echo __('edit.profile'); ?></h1>
   <div class="content">
   <ul>
   
      <?php echo View::factory('user/user_edit_form')->set(array('form' => $form)); ?>
    
      <li><h2><?php echo __('roles'); ?></h2></li>
      <li><table class="content">
         <tr class="heading"><td><?php echo __('role'); ?></td><td><?php echo __('description'); ?></td></tr>
     <?php
         $i = 0;
         foreach($user_roles as $role => $description) {
            echo '<tr';
            if($i % 2 == 0) {
               echo ' class="odd"';
            }
            echo '>';
            echo '<td>'.ucfirst($role).'</td><td>'.$description.'</td>';
            echo '</tr>';
            $i++;
         }
      ?>
            </table>
      </li>
   </ul>
   <br>
<?php
echo $form->submit(NULL, __('save'));
echo $form->close();
?>
   </div>
</div>