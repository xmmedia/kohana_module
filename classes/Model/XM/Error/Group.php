<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Model for `error_group`.
 *
 * @package    XM Template
 * @category   Models
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
class Model_XM_Error_Group extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'error_group';
	// protected $_primary_val = 'name'; // default: name (column used as primary value)
	public $_table_name_display = 'Error Group'; // xm specific
	protected $_log = FALSE; // don't log changes

	// default sorting
	protected $_sorting = array(
		'id' => 'DESC',
	);

	// relationships
	protected $_has_many = array(
		'error_log' => array(
			'model' => 'Error_Log',
			'foreign_key' => 'error_group_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		'id' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'file' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'line' => array(
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
		'data' => array(
			'field_type' => 'Serializable',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	 * Auto-serialize and unserialize columns on get/set.
	 * @var array
	 */
	protected $_serialize_columns = array('data');

	/**
	 * Labels for columns.
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'file' => 'File',
			'line' => 'Line',
			'data' => 'Data',
		);
	}
} // class