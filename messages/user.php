<?php defined('SYSPATH') or die('No direct script access.');

/**
* These messages are for the user model (including user_profile and user_password)
*
* see /system/messages/validation.php for the defaults for each rule. These can be overridden on a per-field/message basis.
*/
return array(
	'username' => array(
		'not_empty' => ':field must not be empty.',
		'invalid' => 'Your username or password is incorrect.',
		'email' => ':field must be an email address.',
		'min_length' => ':field must be an email address.',
		'max_length' => ':field must be an email address.',
		'unique' => 'The username/email address entered is already used. Please use a different email address.',
		'too_many_attempts' => 'There have been too many attempts on this account. Please enter the captcha before continuing.',
		'logged_out' => 'You have been logged out successfully.',
		'not_logged_out' => 'There was a problem logging out.',
	),
	'password' => array(
		'not_empty' => ':field must not be empty.',
		'min_length' => ':field must be at least :param2 characters long.',
		'max_length' => ':field must be less than :param2 characters long.',
		'check_password' => 'Your passwords need to both be the same.',
	),
	'update_profile' => 'Before continuing, please update your profile.',
	'update_password' => 'Before continuing, please change your password.',
	'recaptcha_not_valid' => 'The reCAPTCHA wasn\'t entered correctly. Please try again.',
	'enter_recaptcha' => 'Please enter the reCAPTCHA before logging in.',
	'invalid_token' => 'An invalid or expired security token was received.  Please try again.',
	'login_validation_failed' => 'An error occurred while logging you in, please contact your administrator.'
);