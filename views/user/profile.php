<div class="block">
   <div class="submenu">
      <ul>
         <li><?php echo Html::anchor('user/profile_edit', __('edit.profile')); ?></li>
         <li><?php echo Html::anchor('user/unregister', __('delete.account')); ?></li>
      </ul>
      <br style="clear:both;">
   </div>
   <h1><?php echo __('user.profile') ?></h1>
   <div class="content">
      <p class="intro"><?php echo __('your.user.information'); ?>, <?php echo $user->username ?>.</p>

      <h2><?php echo __('username') ?> &amp; <?php echo __('email') ?> </h2>
      <p><?php echo $user->username ?> &mdash; <?php echo $user->email ?></p>

      <h2><?php echo __('login.activity') ?> </h2>
      <p><?php echo __('last.login').' '.date('F jS, Y', $user->last_login) ?>, at <?php echo date('h:i:s a', $user->last_login) ?>.<br/><?php echo __('total.nbr.logins') ?> <?php echo $user->logins ?></p>

      <?php
      $providers = array_filter(Kohana::$config->load('useradmin.providers'));
      $identities = $user->user_identities->find_all();
      if($identities->count() > 0) {
         echo '<h2>'.__('accounts.associated.with.profile').'</h2><p>';
         foreach($identities as $identity) {
            echo '<a class="associated_account" style="background: #FFF url(/useradmin_assets/img/small/'.$identity->provider.'.png) no-repeat center center"></a>';
            unset($providers[$identity->provider]);
         }
         echo '<br style="clear: both;"></p>';
      }
      if(!empty($providers)) {
         echo '<h2>'.__('additional.accountproviders').'</h2><p>'.__('click.provider.icon.to.associate.account').'</p><p>';
         foreach($providers as $provider => $enabled) {
            if($enabled){
                echo '<a class="associated_account '.$provider.'"
                    style="background: #FFF url(/useradmin_assets/img/small/'.$provider.'_gray.png) no-repeat center center"
                    href="'.URL::site('/user/associate/'.$provider).'"></a>';
            }
         }
         echo '<br style="clear: both;"></p>';
      }
      ?>
   </div>
</div>