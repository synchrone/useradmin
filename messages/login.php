<?php defined('SYSPATH') or die('No direct script access.');

// see /system/mesages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
    'username' => array(
        'not_empty' => __('username.not_empty'),
        'invalid'   => __('username.invalid'),
    ),
    'email' => array(
        'not_empty' => __('email.not_empty'),
        'invalid'   => __('email.invalid'),
    ),
    'password' => array(
        'not_empty' => __('password.not_empty'),
        'invalid'   => __('password.invalid'),
    ),
);

