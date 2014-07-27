<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'routes' => array(
		'login' => TRUE,
		'account' => TRUE,
		'xm_db_admin' => TRUE,
		'model_create' => TRUE,
		'content_admin' => FALSE,
		'user_admin' => TRUE,
		'error_admin' => TRUE,
	),

	// if in production (based on Kohana::$environment), then email the errors including the HTML view including the trace
	'email_exceptions' => TRUE,
);