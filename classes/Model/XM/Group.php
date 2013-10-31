<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Model for `group`.
 *
 * @package    XM
 * @category   Models
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class Model_XM_Group extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'group';
	public $_table_name_display = 'Group';

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// Relationships
	protected $_has_many = array(
		'group_permission' => array(
			'model' => 'Group_Permission',
			'foreign_key' => 'group_id',
		),
		'user_group' => array(
			'model' => 'User_Group',
			'foreign_key' => 'group_id',
		),
		'permission' => array(
			'model' => 'Permission',
			'through' => 'group_permission',
			'foreign_key' => 'group_id',
			'far_key' => 'permission_id',
		),
		'user' => array(
			'model' => 'User',
			'through' => 'user_group',
			'foreign_key' => 'group_id',
			'far_key' => 'user_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
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
	 * Labels for columns.
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'description' => 'Description',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
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
	 * Filter definitions, run everytime a field is set.
	 *
	 * @return  array
	 */
	public function filters() {
		return array(
			'name' => array(
				array('trim'),
			),
			'description' => array(
				array('trim'),
			),
		);
	}
} // class