<?php defined('SYSPATH') or die('No direct script access.');

// see /system/messages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
      'username' => array(
         'username_available' => __('username.already.registered'),
         'username_not_unique' => __('username.already.used'),
       ),
       
      'email' => array(
         'email' => __('email.invalid'), // Workaround for Bug Report #3750
         'email_available' => __('email.already.used'),
         'unique' => __('email.already.used'),
       ),

      'email_confirm' => array(
         'email' => __('email.invalid'), // Workaround for Bug Report #3750
         'email_available' => __('email.already.used'),
         'email_not_unique' => __('email.already.used'),
       ),

      'password' => array(
         'matches'      => __('password.confirm.different'),
      ),

      'password_confirm' => array(
         'matches'      => __('password.confirm.different'),
      ),
);

