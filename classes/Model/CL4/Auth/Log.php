<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_CL4_Auth_Log extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'auth_log';
	public $_table_name_display = 'Auth Log';
	protected $_primary_val = 'username'; // default: name (column used as primary value)
	protected $_log = FALSE; // don't log changes because it's pointless to log changes to a log table

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'id',
		),
	);

	protected $_sorting = array(
		'access_time' => 'DESC',
	);

	// auth types for logging purposes
	// used in auth_type_id in auth_log
	const LOG_TYPE_LOGGED_IN = 1;
	const LOG_TYPE_LOGGED_OUT = 2;
	const LOG_TYPE_INVALID_PASSWORD = 3;
	const LOG_TYPE_INVALID_USERNAME_PASSWORD = 4;
	const LOG_TYPE_UNKNOWN_ERROR = 5;
	const LOG_TYPE_TOO_MANY_ATTEMPTS = 6;
	const LOG_TYPE_VERIFYING_HUMAN = 7;
	const LOG_TYPE_INVALID_TOKEN = 8;

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/cl4/config/cl4orm.php for a full list of cl4-specific options and documentation on what the options do
		*/
		'id' => array(
			'field_type' => 'Hidden',
			'list_flag' => FALSE,
			'edit_flag' => TRUE,
			'search_flag' => FALSE,
			'view_flag' => FALSE,
			'is_nullable' => FALSE,
		),
		'user_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'search_flag' => TRUE,
			'edit_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'sql',
					'data' => "SELECT id, CONCAT_WS('', first_name, ' ', last_name) AS name FROM `user` ORDER BY name",
				),
			),
		),
		'username' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'search_flag' => TRUE,
			'edit_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
			),
		),
		'access_time' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'auth_type_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'array',
					'data' => array(
						Model_Auth_Log::LOG_TYPE_LOGGED_IN => 'Logged In',
						Model_Auth_Log::LOG_TYPE_LOGGED_OUT => 'Logged Out',
						Model_Auth_Log::LOG_TYPE_INVALID_PASSWORD => 'Invalid Password',
						Model_Auth_Log::LOG_TYPE_INVALID_USERNAME_PASSWORD => 'Invalid Username & Password',
						Model_Auth_Log::LOG_TYPE_UNKNOWN_ERROR => 'Unknown Error',
						Model_Auth_Log::LOG_TYPE_TOO_MANY_ATTEMPTS => 'Too Many Attempts',
						Model_Auth_Log::LOG_TYPE_VERIFYING_HUMAN => 'Verifying Human',
					),
				),
			),
		),
		'browser' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'ip_address' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'size' => 15,
				'maxlength' => 15,
			),
		),
	);

	/**
	 * @var array $_display_order The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	protected $_display_order = array(
		10 => 'id',
		20 => 'user_id',
		30 => 'username',
		40 => 'access_time',
		50 => 'auth_type_id',
		60 => 'browser',
		70 => 'ip_address',
	);

	/**
	 * Labels for columns
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'username' => 'Username',
			'access_time' => 'Access Time',
			'auth_type_id' => 'Auth Type',
			'browser' => 'Browser',
			'ip_address' => 'IP Address',
		);
	}
}