<?php defined('SYSPATH') or die('No direct script access.');

// see /system/messages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
	// profile edit
	'profile_saved' => 'Your profile has been saved.',
	'profile_save_error' => 'There was a problem saving your profile. Please try again.',
	'profile_save_validation' => 'Your profile could not be saved because of the following: :validation_errors',
	// password update
	'new_password' => array(
		'not_empty' => 'Your new password cannot be empty.',
		'min_length' => 'Your new password must be at least :param2 characters long.',
		'max_length' => 'Your new password must be less than :param2 characters long.',
	),
	'new_password_confirm' => array(
		'matches' => 'Both the new passwords must be the same.',
	),
	'current_password' => array(
		'not_the_same' => 'Your current password is incorrect.',
	),
	'password_changed' => 'Your password has been changed.',
	'password_change_error' => 'There was a problem updating your password. Please try again.',
	'password_change_validation' => 'Your password could not be changed because of the following:',
);