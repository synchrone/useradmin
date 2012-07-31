<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set('user/provider', 'user/provider(/<provider>)', array('provider' => '.+'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'provider',
		'provider'       => NULL,
	));

Route::set('user/provider_return', 'user/provider_return(/<provider>)', array('provider' => '.+'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'provider_return',
		'provider'       => NULL,
	));
Route::set('user/default', 'user(/<action>(/<provider>))',
	array(
		'action' => '(provider|provider_return|associate|associate_return)',
		'provider' => '.+',
	))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'index',
		'provider'   => NULL,
	));
Route::set('user/admin', 'admin_user(/<action>(/<id>))',
	array(
		'action' => '(edit|delete)',
	))
	->defaults(array(
		'controller' => 'admin_user',
		'action'     => 'index',
		'id'   => NULL,
	));

// Static file serving (CSS, JS, images)
Route::set('assets', 'useradmin_assets/<dir>(/<file>)', array('file' => '.+', 'dir' => '(css|img|js)'))
   ->defaults(array(
		'controller' => 'user',
		'action'     => 'media',
		'file'       => NULL,
		'dir'       => NULL,
	));

//set the cookie salt if it is not yet set
if( ! Cookie::$salt )
{
    $auth = Kohana::$config->load('useradmin.auth');
    Cookie::$salt = $auth['cookie_salt'];
}

