<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default permission
 */
class Model_CL4_Permission extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'permission';
	public $_table_name_display = 'Permission';

	// Relationships
	protected $_has_many = array(
		'group' => array(
			'through' => 'group_permission',
		),
	);

	protected $_sorting = array(
		'name' => 'ASC',
		'permission' => 'ASC',
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
			'view_flag' => FALSE,
			'search_flag' => FALSE,
			'is_nullable' => FALSE,
		),
		'permission' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'size' => 75,
				'maxlength' => 255,
			),
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
				'maxlength' => 150,
			),
		),
		'description' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 500,
			),
		),
	);

	/**
	 * @var array $_display_order The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	protected $_display_order = array(
		10 => 'id',
		20 => 'permission',
		30 => 'name',
		40 => 'description',
	);

	/**
	 * Rule definitions for validation
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'permission' => array(
				array('not_empty'),
				array('max_length', array(':value', 255)),
			),
			'name' => array(
				array('not_empty'),
				array('max_length', array(':value', 150)),
			),
			'description' => array(
				array('max_length', array(':value', 500)),
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
			'permission' => 'Permission',
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