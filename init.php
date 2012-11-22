<?php defined('SYSPATH') or die ('No direct script access.');

$routes = Kohana::$config->load('xm.routes');

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
	Route::set('dbchange', 'db_change(/<action>)')
		->defaults(array(
			'controller' => 'DB_Change',
			'action' => NULL,
	));
}