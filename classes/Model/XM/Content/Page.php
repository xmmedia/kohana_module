<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `content_page`.
 *
 * @package    XM
 * @category   Content Admin
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Content_Page extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'content_page';
	public $_table_name_display = 'Content Page'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'content' => array(
			'model' => 'Content',
			'foreign_key' => 'content_page_id',
		),
		'content_history' => array(
			'model' => 'Content_History',
			'through' => 'content',
			'foreign_key' => 'content_page_id',
			'far_key' => 'content_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/xm/config/xmorm.php for a full list of cl4-specific options and documentation on what the options do
		*/
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
		'url' => array(
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
			'url' => 'URL',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	public function rules() {
		return array(
			'name' => array(array('not_empty')),
			'url' => array(array('not_empty')),
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
			'url' => array(array('trim')),
		);
	}
} // class