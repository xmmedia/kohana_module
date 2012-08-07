<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_XM_Tree extends ORM {
	//protected $_db_group = 'default'; // or any group in database configuration
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'tree';
	public $_table_name_display = 'Tree'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'lft' => 'ASC',
		'rgt' => 'ASC',
	);

	// relationships
	//protected $_has_one = array();
	//protected $_has_many = array();
	//protected $_belongs_to = array();

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
		'expiry_date' => array(
			'field_type' => 'datetime',
			'is_nullable' => FALSE,
		),
		'name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
			),
		),
		'lft' => array(
			'field_type' => 'text',
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
		'rgt' => array(
			'field_type' => 'text',
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
	);

	/**
	 * @var  array  $_created_column  The date and time this row was created.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	//protected $_created_column = array('column' => 'date_created', 'format' => 'Y-m-j H:i:s');

	/**
	 * @var  array  $_updated_column  The date and time this row was updated.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	//protected $_updated_column = array('column' => 'date_modified', 'format' => TRUE);

	/**
	 * @var  array  $_expires_column  The time this row expires and is no longer returned in standard searches.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_expires_column = array(
		'column' 	=> 'expiry_date',
		'default'	=> 0,
	);

	/**
	 * @var  array  $_display_order  The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	/*
	protected $_display_order = array(
		'id',
		'expiry_date',
		'name',
		'lft',
		'rgt',
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
			'expiry_date' => 'Expiry Date',
			'name' => 'Name',
			'lft' => 'Left',
			'rgt' => 'Right',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	/*
	public function rules() {
		return array();
	}
	*/

	/**
	* Filter definitions, run everytime a field is set
	*
	* @return  array
	*/
	/*
	public function filters() {
		return array(TRUE => array(array('trim')),);
	}
	*/

	public function set_edit_fields() {
		return $this->set_table_columns('lft', 'edit_flag', FALSE)
			->set_table_columns('rgt', 'edit_flag', FALSE);
	}
} // class