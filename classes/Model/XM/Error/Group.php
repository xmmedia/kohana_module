<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Model for `error_group`.
 *
 * @package    XM Template
 * @category   Models
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class Model_XM_Error_Group extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'error_group';
	// protected $_primary_val = 'name'; // default: name (column used as primary value)
	public $_table_name_display = 'Error Group'; // xm specific

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
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	 * Labels for columns.
	 *
	 * @return  array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
		);
	}
} // class