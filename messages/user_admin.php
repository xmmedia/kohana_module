<?php defined('SYSPATH') or die ('No direct script access.');

return Kohana::message('user') + array(
	'error_viewing' => 'There was an error while viewing the user.',
	'error_preparing_email' => 'There was a problem preparing to send the email.',
	'email_password_sent' => 'The login information and new password has been sent to the user.',
	'email_account_info' => 'The user\'s login information was sent in an email.',
	'user_saved' => 'The user has been saved.',
	'user_deleted' => 'The user was deleted.',
	'group_permissions_updated' => 'The permissions for the group were updated:count',
	'group_users_updated' => 'The users in the group were updated:count',
	'other_groups' => 'This user is apart of permission groups that you are unable to change: :other_groups. If you would like any of these changed, please contact an administrator.',
);