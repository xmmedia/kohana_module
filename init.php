<?php defined('SYSPATH') or die ('No direct script access.');

$routes = Kohana::$config->load('xm.routes');

if ($routes['useradmin']) {
	Route::set('useradmin', 'useradmin(/<action>(/<id>))')
		->defaults(array(
			'controller' => 'useradmin',
			'action' => NULL,
	));
}

if ($routes['content_admin']) {
	// route for content admin
	Route::set('content_admin', 'content_admin(/<action>(/<id>))')
		->defaults(array(
			'controller' => 'content',
			'action' => 'index',
	));
}

if ($routes['dbchange']) {
	Route::set('dbchange', 'dbchange(/<action>)')
		->defaults(array(
			'controller' => 'dbchange',
			'action' => NULL,
	));
}