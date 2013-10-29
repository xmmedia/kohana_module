<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default permission
 */
class Model_CL4_Group extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'group';
	public $_table_name_display = 'Group';

	// Relationships
	protected $_has_many = array(
		'user' => array(
			'through' => 'user_group',
			'far_key' => 'user_id',
			'foreign_key' => 'group_id',
		),
		'permission' => array(
			'through' => 'group_permission',
			'far_key' => 'permission_id',
			'foreign_key' => 'group_id',
		),
	);

	protected $_sorting = array(
		'name' => 'ASC',
	);

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
		'name' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'size' => 75,
				'maxlength' => 100,
			),
		),
		'description' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	 * @var array $_display_order The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	protected $_display_order = array(
		10 => 'id',
		20 => 'name',
		30 => 'description',
	);

	/**
	 * Rule definitions for validation
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 100)),
			),
		);
	}

	/**
	 * Labels for columns
	 *
	 * @return array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
		);
	}

	/**
	 * Filter definitions, run everytime a field is set
	 *
	 * @return array
	 */
	public function filters() {
		return array(
			'name' => array(array('trim')),
			'description' => array(array('trim')),
		);
	}
} // class