<?php defined('SYSPATH') or die ('No direct script access.');

if ( ! defined('DEFAULT_LANG')) {
	/**
	* setting the default language if it's not already set
	* if set to NULL, then the route won't include a language by default
	* if you want a language in the route, set default_lang to the language (ie, en-ca)
	*/
	define('DEFAULT_LANG', NULL);
}

if ( ! isset($lang_options)) {
	$lang_options = '(en-ca|fr-ca)';
}

$routes = Kohana::$config->load('xm.routes');

if ($routes['useradmin']) {
	Route::set('useradmin', '(<lang>/)useradmin(/<action>(/<id>))', array('lang' => $lang_options))
		->defaults(array(
			'controller' => 'useradmin',
			'lang' => DEFAULT_LANG,
			'action' => NULL,
	));
}

if ($routes['dbchange']) {
	Route::set('dbchange', '(<lang>/)dbchange(/<action>)', array('lang' => $lang_options))
		->defaults(array(
			'controller' => 'dbchange',
			'lang' => DEFAULT_LANG,
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

if ($routes['tree']) {
	// tree route
	Route::set('tree', 'tree(/<action>)')
		->defaults(array(
			'controller' => 'tree',
	));
}