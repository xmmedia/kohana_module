<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Model for `user_reset`.
 *
 * @package    XM Kohana Module
 * @category   Models
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class Model_XM_User_Reset extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'user_reset';
	// protected $_primary_val = 'name'; // default: name (column used as primary value)
	public $_table_name_display = 'User - Reset'; // xm specific

	// default sorting
	protected $_sorting = array(
		'datetime' => 'DESC',
	);

	// relationships
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		'id' => array(
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'user_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
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
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 32,
			),
		),
		'datetime' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	 * @var  array  $_created_column  The date and time this row was created.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_created_column = array('column' => 'datetime', 'format' => 'Y-m-j H:i:s');

	/**
	 * Labels for columns.
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'token' => 'Token',
			'datetime' => 'Date Time',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'user_id' => array(
				array('selected'),
			),
			'token' => array(
				array('not_empty'),
				array('min_length', array(':value', 32)),
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
			'token' => array(
				array('trim'),
			),
		);
	}
} // class