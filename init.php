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

$routes = Kohana::config('xm.routes');

if ($routes['dbchange']) {
	Route::set('dbchange', '(<lang>/)dbchange(/<action>)', array('lang' => $lang_options))
		->defaults(array(
			'controller' => 'dbchange',
			'lang' => DEFAULT_LANG,
			'action' => NULL,
	));
}