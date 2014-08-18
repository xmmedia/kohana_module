<?php defined('SYSPATH') or die ('No direct script access.');

return array(
	'valid_date'   => 'A valid date must be entered for :field',
	'greater_than' => 'A value greater than :param2 must be entered for :field',
	'not_in_array' => 'Choose a different value for :field. The current value is not allowed',
	'selected'     => 'A value must be selected for :field',
	'checked'      => ':field must be checked',
	'phone_with_ext' => ':field must be a phone number',

	// this is a correction for the kohana validation message (a -> an)
	'email'        => ':field must be an email address',
);