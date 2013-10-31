<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using XM_ORM and should provide
 * standard Kohana ORM features in additon to xm-specific features.
 */
class Model_XM_Tree extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'tree';
	public $_table_name_display = 'Tree'; // xm specific

	// default sorting
	protected $_sorting = array(
		'lft' => 'ASC',
		'rgt' => 'ASC',
	);

	// column definitions
	protected $_table_columns = array(
		'id' => array(
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'expiry_date' => array(
			'field_type' => 'DateTime',
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
				'maxlength' => 100,
			),
		),
		'lft' => array(
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
		'rgt' => array(
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
	);

	/**
	 * @var  array  $_expires_column  The time this row expires and is no longer returned in standard searches.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_expires_column = array(
		'column' 	=> 'expiry_date',
		'default'	=> 0,
	);

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
	public function rules() {
		return array(
			'name' => array(
				array('not_empty'),
			),
		);
	}

	/**
	* Filter definitions, run everytime a field is set
	*
	* @return  array
	*/
	public function filters() {
		return array(
			'name' => array(array('trim')),
		);
	}

	/**
	 * Sets the lft (left) and rgt (right) fields to non-editable so they can be set the user through a form post.
	 *
	 * @return ORM
	 */
	public function set_edit_fields() {
		return $this->set_table_columns('lft', 'edit_flag', FALSE)
			->set_table_columns('rgt', 'edit_flag', FALSE);
	}

	/**
	 * Returns the name for the node.
	 * Useful when the name is not a property of the object, but comes from a relationship.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}
} // class