<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_XM_Change_Script extends ORM {
	protected $_table_name = 'change_script';
	public $_table_name_display = 'Change Scripts'; // cl4-specific

	// default sorting
	protected $_sorting = array('applied' => 'DESC');

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
		'filename' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 250,
				'size' => 50,
			),
		),
		'type' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 10,
				'size' => 10,
			),
		),
		'applied' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'description' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'log' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	 * @var timestamp $_created_column The time this row was created and/or applied.
	 *
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_created_column = array(
		'column' => 'applied',
		'format' => 'Y-m-j H:i:s'
	);

	/**
	* Labels for columns
	*
	* @return  array
	*/
	public function labels() {
		return array(
			'id' => 'ID',
			'filename' => 'Filename',
			'type' => 'Type',
			'applied' => 'Applied',
			'description' => 'Description',
			'log' => 'Log',
		);
	}
} // class