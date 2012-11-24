<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using cl4_ORM and should provide
 * standard Kohana ORM features in additon to cl4-specific features.
 */
class Model_XM_Country extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'country';
	public $_table_name_display = 'Country'; // cl4-specific

	// column labels
	protected $_labels = array(
		'id' => 'ID',
		'expiry_date' => 'Expiry Date',
		'name' => 'Name',
		'symbol' => 'Symbol',
		'exchange_rate' => 'Exchange Rate',
		'code' => 'Code',
		'currency_code' => 'Currency Code',
		'display_order' => 'Display Order',
	);

	// default sorting
	protected $_sorting = array(
		'display_order' => 'ASC',
		'name' => 'ASC',
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
		'name' => array(
			'field_type' => 'Text',
			'display_order' => 30,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 50,
			),
		),
		'symbol' => array(
			'field_type' => 'Text',
			'display_order' => 40,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 5,
				'size' => 5,
			),
		),
		'exchange_rate' => array(
			'field_type' => 'Text',
			'display_order' => 50,
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 13,
				'size' => 13,
			),
		),
		'code' => array(
			'field_type' => 'Text',
			'display_order' => 60,
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
		'currency_code' => array(
			'field_type' => 'Text',
			'display_order' => 70,
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
		'display_order' => array(
			'field_type' => 'Text',
			'display_order' => 80,
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
			'symbol' => 'Symbol',
			'exchange_rate' => 'Exchange Rate',
			'code' => 'Code',
			'currency_code' => 'Currency Code',
			'display_order' => 'Display Order',
		);
	}
} // class