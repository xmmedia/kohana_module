<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_CL4_Session extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'session';
	protected $_primary_key = 'session_id'; // default: id
	protected $_primary_val = 'session_id'; // default: name (column used as primary value)
	public $_table_name_display = 'Session'; // cl4-specific

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/cl4/config/cl4orm.php for a full list of cl4-specific options and documentation on what the options do
		*/
		'session_id' => array(
			'field_type' => 'Select',
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'Session',
				),
			),
		),
		'last_active' => array(
			'field_type' => 'Text',
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 10,
				'size' => 10,
			),
		),
		'contents' => array(
			'field_type' => 'TextArea',
			'is_nullable' => FALSE,
		),
	);

	/**
	 * @var array $_display_order The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	protected $_display_order = array(
		10 => 'session_id',
		20 => 'last_active',
		30 => 'contents',
	);

	/**
	 * Labels for columns
	 *
	 * @return array
	 */
	public function labels() {
		return array(
			'session_id'  => 'Session',
			'last_active' => 'Last Active',
			'contents'    => 'Contents',
		);
	} // function labels
} // class Model_Session