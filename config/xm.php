<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'routes' => array(
		'login' => TRUE,
		'account' => TRUE,
		'xmadmin' => TRUE,
		'model_create' => TRUE,
		'content_admin' => FALSE,
		'db_change' => TRUE,
		'user_admin' => TRUE,
		'change_script' => FALSE,
	),

	// if in production (based on Kohana::$environment), then email the errors including the HTML view including the trace
	'email_exceptions' => TRUE,
);