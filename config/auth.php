<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'enable_3.0.x_hashing' => FALSE, // set to TRUE to enable the same hashing as was in Kohana 3.0.9
	'driver'       => 'ORM', // the driver to use; we're defaulting to ORM
	'remember_lifetime' => 1209600, // 14 days
	'auth_lifetime'  => 10800, // 3 hours: the amount of time till the user will have to enter their password to continue using the site; set to 0 for unlimited
	'timed_out_max_lifetime' => 172800, // 2 days: the amount of time till the user will have to fully login again and will not be able to login through the timed out password only page; set to 0 for unlimited
	'timestamp_key'  => 'auth_timestamp',
	'default_login_redirect' => 'cl4admin', // the route to redirect the user after they login; used within Controller_CL4_login::login_success_redirect()
	'default_login_redirect_params' => array(), // the parameters to pass to the Route for the default login redirect
);