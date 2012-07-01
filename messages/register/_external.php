<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'password_confirm' => array( //this field comes from additional validation
        'not_empty' => __('password.not_empty'),
        'matches' => __('password.confirm.different'),
    ),
);