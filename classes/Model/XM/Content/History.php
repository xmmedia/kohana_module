<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `content_history`.
 *
 * @package    XM
 * @category   Content Admin
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Content_History extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'content_history';
	public $_table_name_display = 'Content History'; // xm specific

	// default sorting
	protected $_sorting = array(
		'creation_date' => 'DESC',
		'post_date' => 'DESC',
		'history_date' => 'DESC',
	);

	// relationships
	protected $_belongs_to = array(
		'content_item' => array(
			'model' => 'Content',
			'foreign_key' => 'content_id',
		),
		'creation_user' => array(
			'model' => 'User',
			'foreign_key' => 'creation_user_id',
		),
		'post_user' => array(
			'model' => 'User',
			'foreign_key' => 'post_user_id',
		),
		'history_user' => array(
			'model' => 'User',
			'foreign_key' => 'history_user_id',
		),
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
		'content_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'Content',
				),
			),
		),
		'creation_date' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'creation_user_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'sql',
					'data' => "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM user ORDER BY first_name, last_name",
				),
			),
		),
		'post_date' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'post_user_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'sql',
					'data' => "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM user ORDER BY first_name, last_name",
				),
			),
		),
		'history_date' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'history_user_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'sql',
					'data' => "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM user ORDER BY first_name, last_name",
				),
			),
		),
		'content' => array(
			'field_type' => 'HTML',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'class' => 'tinymce',
			),
		),
		'comments' => array(
			'field_type' => 'TextArea',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 1000,
				'rows' => 2,
			),
		),
	);

	/**
	 * @var  array  $_created_column  The date and time this row was created.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_created_column = array('column' => 'creation_date', 'format' => 'Y-m-j H:i:s');

	/**
	 * @var  array  $_expires_column  The time this row expires and is no longer returned in standard searches.
	 */
	protected $_expires_column = array(
		'column' 	=> 'expiry_date',
		'default'	=> '0000-00-00 00:00:00',
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
			'content_id' => 'Content',
			'creation_date' => 'Creation Date',
			'creation_user_id' => 'Creation User',
			'post_date' => 'Post Date',
			'post_user_id' => 'Post User',
			'history_date' => 'History Date',
			'history_user_id' => 'History User',
			'content' => 'Content',
			'comments' => 'Comments Regarding Change',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	public function rules() {
		return array(
			'content' => array(array('not_empty')),
		);
	}
} // class