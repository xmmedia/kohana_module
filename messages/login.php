<?php defined('SYSPATH') or die ('No direct script access.');

return array(
	'error' => 'There was a problem logging in or preparing the login form. Please try again.',
	// forgot password
	'reset_send_error' => 'There was a problem sending your password reset link. The administrator has been notified.',
	'reset_admin_account' => 'This password for this account cannot be reset using this method.',
	'reset_not_found' => 'The username cannot be found.',
	'reset_error' => 'There was a problem with the forgot password. Please try again later.',
	'reset_email_empty' => 'Enter your email address to continue.',
	'token_not_found' => 'Unable to retrieve your reset information. Please enter your email address again, try copying and pasting the link from the email, or contact the administrator.',
	'password_email_partial' => 'Only partial information was received to reset your password. Please try copying and pasting the link from the email or contact the administrator.',
	'password_min_length' => 'Please enter a password with a minimum length of ' . (int) Kohana::$config->load('auth.password_min_length') . ' characters.',
	'passwords_different' => 'The passwords you entered are different. Please enter the same password in both fields.',
	'password_saved' => 'Your password has been reset. Please login.',
);