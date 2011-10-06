<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_XM_Request_Log extends ORM {
	protected $_table_name = 'request_log';
	//protected $_primary_val = 'name'; // default: name (column used as primary value)
	public $_table_name_display = 'Request Log'; // cl4-specific

	protected $_log = FALSE;

	// relationships
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/cl4/config/cl4orm.php for a full list of cl4-specific options and documentation on what the options do
		*/
		'id' => array(
			'field_type' => 'hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'datetime' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'user_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'sql',
					'data' => "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM user ORDER BY first_name, last_name",
				),
			),
		),
		'path' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'get' => array(
			'field_type' => 'textarea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'post' => array(
			'field_type' => 'textarea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	//protected $_serialize_columns = array('get', 'post');

	/**
	 * @var  array  $_display_order  The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	/*
	protected $_display_order = array(
		'id',
		'datetime',
		'user_id',
		'path',
		'get',
		'post',
	);
	*/

	/**
	* Labels for columns
	*
	* @return  array
	*/
	public function labels() {
		return array(
			'id' => 'ID',
			'datetime' => 'Date & Time',
			'user_id' => 'User',
			'path' => 'Path',
			'get' => 'Get Vars',
			'post' => 'Post Vars',
		);
	}

	/**
	* Adds a new request log record using the most efficient method (see classes/bench/requestlog) within reason.
	* This may take slightly longer than using the PHP MySQL methods, but the difference is very small (0.0002s).
	* Stores the datetime, user, path, get and post.
	* The keys found in config/request_log.remove_keys are removed from the post and get vars.
	* Pass in $data to override the automatically retrieved values.
	*
	* @param  array  $data  Optional data to replace the values found automatically
	* @return  void
	*/
	public static function store_request($data = array()) {
		$remove_keys = (array) Kohana::$config->load('request_log.remove_keys');

		$post = $_POST;
		Arr::recursive_unset($post, $remove_keys);

		$get = $_GET;
		Arr::recursive_unset($get, $remove_keys);

		if ( ! array_key_exists('user_id', $data)) {
			$user = Auth::instance()->get_user();
			if ( ! empty($user) && $user->loaded()) {
				$data['user_id'] = $user->id;
			}
		}

		$_data = array(
			'datetime' => (array_key_exists('datetime', $data) ? $data['datetime'] : DB::expr("NOW()")),
			'user_id' => (array_key_exists('user_id', $data) ? $data['user_id'] : 0),
			'path' => (array_key_exists('path', $data) ? $data['path'] : Request::current()->uri()),
			'get' => json_encode(array_key_exists('get', $data) ? $data['get'] : $get),
			'post' => json_encode(array_key_exists('post', $data) ? $data['post'] : $post),
		);

		DB::insert('request_log', array_keys($_data))
			->values($_data)
			->execute();
	} // function store_request
} // class