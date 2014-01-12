<?php defined('SYSPATH') or die ('No direct script access.');

return array(
	// the number of minutes since the last time the error occurred to send an email notification
	'reoccurance_email_time' => 120,
	// when set to FALSE, the system will not send the error email immediately
	'send_error_immediately' => FALSE,
	// if TRUE, errors will be stored in files for parsing with the error admin tools
	'use_error_admin' => TRUE,
	// the maximum number of errors clicking the "parse" link will parse
	// this is to avoid taking too much memory and/or too long
	'web_parse_limit' => 25,
);