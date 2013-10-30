<?php defined('SYSPATH') or die ('No direct script access.');

// define some constants that make it easier to add line endings
if ( ! defined('EOL')) {
	/**
	*   CONST :: end of line
	*   @var    string
	*/
	define('EOL', "\r\n");
}

if ( ! defined('HEOL')) {
	/**
	*   CONST :: HTML line ending with new line
	*   @var    string
	*/
	define('HEOL', "<br />\r\n");
}

if ( ! defined('TAB')) {
	/**
	*   CONST :: HTML line ending with new line
	*   @var    string
	*/
	define('TAB', "\t");
}

$routes = Kohana::$config->load('xm.routes');

if ($routes['login']) {
	// login page
	Route::set('login', 'login(/<action>)', array('action' => '[a-z_]{0,}',))
		->defaults(array(
			'controller' => 'Login',
			'action' => NULL,
	));
}

if ($routes['account']) {
	// account: profile, change password, forgot, register
	Route::set('account', 'account(/<action>)', array('action' => '[a-z_]{0,}',))
	->defaults(array(
		'controller' => 'Account',
		'action' => 'index',
	));
}

if ($routes['xm_db_admin']) {
	// claero admin
	// Most cases: /dbadmin/user/edit/2
	// Special case for download: /dbadmin/demo/download/2/public_filename
	// Special case for add_multiple: /dbadmin/demo/add_mulitple/5 (where 5 is the number of records to add)
	Route::set('xm_db_admin', 'dbadmin(/<model>(/<action>(/<id>(/<column_name>))))', array(
		'model' => '[a-zA-Z0-9_]{0,}',
		'action' => '[a-z_]+',
		'id' => '\d+',
		'column_name' => '[a-z_]+')
	)->defaults(array(
		'controller' => 'XMAdmin',
		'model' => NULL, // this is the default object that will be displayed when accessing xm_db_admin (dbadmin) without a model
		'action' => 'index',
		'id' => NULL,
		'column_name' => NULL,
	));
}

if ($routes['model_create']) {
	// model create
	Route::set('model_create', 'model_create(/<model>/<action>)', array(
		'model' => '[a-zA-Z0-9_]{0,}',
	))->defaults(array(
		'controller' => 'Model_Create',
		'action' => 'index',
		'model' => NULL,
	));
}

if ($routes['user_admin']) {
	Route::set('user_admin', 'user_admin(/<action>(/<id>))')
		->defaults(array(
			'controller' => 'User_Admin',
			'action' => NULL,
	));
}

if ($routes['content_admin']) {
	// route for content admin
	Route::set('content_admin', 'content_admin(/<action>(/<id>))')
		->defaults(array(
			'controller' => 'Content',
			'action' => 'index',
	));
}

if ($routes['db_change']) {
	Route::set('db_change', 'db_change(/<action>)')
		->defaults(array(
			'controller' => 'DB_Change',
			'action' => NULL,
	));
}