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

if ($routes['change_script'] && Kohana::$is_cli) {
	// change script route: for running db and other upgrade change scripts
	Route::set('change_script', 'change_script(/<action>)')
		->defaults(array(
			'controller' => 'change_script',
	));
}