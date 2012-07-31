<?php defined('SYSPATH') or die('No direct script access.');

// see /system/messages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
    'username' => array(
        'unique' => __('username.already.used'),
    ),

    'email' => array(
        'email' => __('email.invalid'), // Workaround for Bug Report #3750
        'unique' => __('email.already.used'),
    ),
    'password' => array(
        'matches'      => __('password.confirm.different'),
    ),
);

