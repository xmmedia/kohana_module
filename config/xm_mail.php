<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'default' => array(
		'debug' => (KOHANA_ENVIRONMENT > Kohana::PRODUCTION), // If we should be performing debug actions
		'language' => 'en', // The language to send emails in
		'from' => 'webmaster@example.com', // The email from which all emails will come from
		'from_name' => 'Website', // The name from which the email will come from (attached to the email address)
		'log_email' => NULL, // The email address to BCC all emails to when not in debug.
		'debug_email' => NULL, // The email address to send to when the email is not in `$allowed_debug_emails`.
		'allowed_debug_emails' => array(), // The email addresses that can be sent to while in debug mode (so users don't get test emails).
		'mailer' => 'smtp', // SMTP or sendmail
		'char_set' => 'utf-8', // The character set for the emails
		// Configuration options for STMP server
		'smtp' => array(
			'host' => 'localhost', // SMTP server hostname
			'username' => NULL, // SMTP server username
			'password' => NULL, // SMTP server password
			'port' => 25, // SMTP server port
			'timeout' => NULL, // Timeout for sending mail
			'secure' => NULL, // Security, for example GMail uses "tls"
		),
		// reply to address & name
		'reply_to' => array(
			'email' => NULL,
			'name' => NULL,
		),

		// Config for adding a user to To field using a query
		'user_table' => array(
			'model' => 'User', // The model to select from
			'email_field' => 'username', // The field to get the email address from
			'first_name_field' => 'first_name', // The field to get the first name from
			'last_name_field' => 'last_name', // The field to get the last name from
		),

		// Whether PHPMailer should throw exceptions
		'phpmailer_throw_exceptions' => TRUE,
	),
);