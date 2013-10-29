<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `content`.
 *
 * @package    XM
 * @category   Content Admin
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Content extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'content';
	public $_table_name_display = 'Content'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'content_history' => array(
			'model' => 'Content_History',
			'foreign_key' => 'content_id',
		),
	);
	protected $_belongs_to = array(
		'last_update_user' => array(
			'model' => 'User',
			'foreign_key' => 'last_update_user_id',
		),
		'content_page' => array(
			'model' => 'Content_Page',
			'foreign_key' => 'content_page_id',
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
		'last_update' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'last_update_user_id' => array(
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
		'code' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
				'size' => 50,
			),
			'view_in_edit_mode' => TRUE,
		),
		'name' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'size' => 50,
				'maxlength' => 150,
			),
		),
		'content_page_id' => array(
			'field_type' => 'Select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'Content_Page',
				),
			),
		),
		'description' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'size' => 100,
				'maxlength' => 500,
			),
		),
		'instructions' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 1000,
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
		'text_only_flag' => array(
			'field_type' => 'Checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
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
			'last_update' => 'Last Update',
			'last_update_user_id' => 'Last Update User',
			'code' => 'Code',
			'name' => 'Name',
			'content_page_id' => 'Content Page',
			'description' => 'Description',
			'instructions' => 'Instructions',
			'content' => 'Content',
			'text_only_flag' => 'Text Only',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	public function rules() {
		return array(
			'code' => array(array('not_empty')),
			'name' => array(array('not_empty')),
		);
	}

	/**
	* Filter definitions, run everytime a field is set
	*
	* @return  array
	*/
	public function filters() {
		return array(
			'code' => array(array('trim')),
			'name' => array(array('trim')),
			'description' => array(array('trim')),
			'instructions' => array(array('trim')),
		);
	}

	/**
	 * Returns the current draft for the current content item.
	 *
	 * @return  Model_Content_History
	 */
	public function get_draft() {
		return $this->content_history
			->where('content_history.creation_date', '>', 0)
			->where('content_history.post_date', '=', 0)
			->where('content_history.history_date', '=', 0)
			->find();
	}

	/**
	 * Returns TRUE if there's a draft for the current content item.
	 *
	 * @return  boolean
	 */
	public function has_draft() {
		return $this->get_draft()
			->loaded();
	}

	/**
	 * Returns the formatted version of the last update to the current content item and the user's name.
	 *
	 * @return  string
	 */
	public function last_update() {
		if ( ! Form::check_date_empty_value($this->last_update)) {
			return $this->last_update . ' by ' . $this->last_update_user->name();
		} else {
			return 'Unknown';
		}
	}

	/**
	 * Same as parent _load_result(), but after the result is load, when not multiple, a check is made for text only content.
	 * If it's text only, then the field is changed to a text area and the "tinymce" class is changed to "content_text_only" (if class is set).
	 *
	 * @chainable
	 * @param  bool $multiple Return an iterator or load a single row
	 * @return ORM|Database_Result
	 */
	protected function _load_result($multiple = FALSE) {
		$return = parent::_load_result($multiple);

		if ($multiple !== TRUE && $this->text_only_flag) {
			$this->_table_columns['content']['field_type'] = 'TextArea';
			if (isset($this->_table_columns['content']['field_attributes']['class'])) {
				$this->_table_columns['content']['field_attributes']['class'] = UTF8::substr_replace('tinymce', 'content_text_only', $this->_table_columns['content']['field_attributes']['class']);
			}
		}

		return $return;
	}
} // class