<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_CL4_User_Token extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'user_token';
	protected $_primary_val = 'user_id'; // default: name (column used as primary value)
	public $_table_name_display = 'User Token'; // cl4-specific

	// relationships
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
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
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_created' => array(
			'field_type' => 'Hidden',
			'is_nullable' => FALSE,
		),
		'date_expired' => array(
			'field_type' => 'DateTime',
			'is_nullable' => FALSE,
		),
		'user_id' => array(
			'field_type' => 'Select',
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'User',
				),
			),
		),
		'token' => array(
			'field_type' => 'Text',
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 35,
			),
		),
	);

	/**
	 * @var timestamp $_created_column The time this row was created.
	 *
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_created_column = array('column' => 'date_created', 'format' => 'Y-m-j H:i:s');

	/**
	 * @var array $_display_order The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	protected $_display_order = array(
		10 => 'id',
		20 => 'date_created',
		30 => 'date_expired',
		40 => 'user_id',
		50 => 'token',
	);

	/**
	 * Labels for columns
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'date_created' => 'Date Created',
			'date_expired' => 'Date Expired',
			'user_id' => 'User',
			'token' => 'Token',
		);
	}
}