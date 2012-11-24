<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_XM_State extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'state';
	public $_table_name_display = 'State'; // cl4-specific

	// column labels
	protected $_labels = array(
		'id' => 'ID',
		'expiry_date' => 'Expiry Date',
		'country_id' => 'Country',
		'name' => 'Name',
		'abbrev' => 'Abbrev',
		'alternate' => 'Alternate',
		'display_order' => 'Display Order',
	);

	// default sorting
	protected $_sorting = array(
		'display_order' => 'ASC',
		'name' => 'ASC',
	);

	// relationships
	protected $_has_one = array(
		'country' => array(
			'model' => 'country',
			'through' => 'country',
			'foreign_key' => 'id',
			'far_key' => 'country_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/cl4/config/cl4orm.php for a full list of cl4-specific options and documentation on what the options do
		*/
		'id' => array(
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'display_order' => 10,
			'is_nullable' => FALSE,
		),
		'expiry_date' => array(
			'field_type' => 'DateTime',
			'display_order' => 20,
			'is_nullable' => FALSE,
		),
		'country_id' => array(
			'field_type' => 'Select',
			'display_order' => 30,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'country',
				),
			),
		),
		'name' => array(
			'field_type' => 'Text',
			'display_order' => 40,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
			),
		),
		'abbrev' => array(
			'field_type' => 'Text',
			'display_order' => 50,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 3,
				'size' => 3,
			),
		),
		'alternate' => array(
			'field_type' => 'Text',
			'display_order' => 60,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'display_order' => array(
			'field_type' => 'Text',
			'display_order' => 70,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 6,
				'size' => 6,
			),
		),
	);

	/**
	 * @var timestamp $_expires_column The time this row expires and is no longer returned in standard searches.
	 *
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_expires_column = array(
		'column' 	=> 'expiry_date',
		'format' 	=> 'Y-m-j H:i:s',
		'default'	=> 0,
	);
} // class