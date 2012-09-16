<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_tax`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Tax extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_tax';
	public $_table_name_display = 'Cart - Tax'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'cart_order' => array(
			'model' => 'cart_order',
			'through' => 'cart_order_tax',
			'foreign_key' => 'cart_tax_id',
			'far_key' => 'cart_order_id',
		),
		'cart_order_tax' => array(
			'model' => 'cart_order_tax',
			'foreign_key' => 'cart_tax_id',
		),
	);
	protected $_belongs_to = array(
		'country' => array(
			'model' => 'country',
			'foreign_key' => 'country_id',
		),
		'state' => array(
			'model' => 'state',
			'foreign_key' => 'state_id',
		),
	);

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/cl4/config/cl4orm.php for a full list of cl4-specific options and documentation on what the options do
		*/
		'id' => array(
			'field_type' => 'hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'expiry_date' => array(
			'field_type' => 'datetime',
			'is_nullable' => FALSE,
		),
		'name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 50,
			),
		),
		'start' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'end' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'country_id' => array(
			'field_type' => 'select',
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
		'state_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'state',
				),
			),
		),
		'together_flag' => array(
			'field_type' => 'checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'priority' => array(
			'field_type' => 'text',
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
		'calculation_method' => array(
			'field_type' => 'radios',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'array',
					'data' => array(
						'%' => 'Percentage (%)',
						'$' => 'Dollar Value ($)'
					),
				),
				'default_value' => '%',
			),
		),
		'amount' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 9,
				'size' => 9,
				'class' => 'numeric',
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
			'date_expired' => 'Date Expired',
			'name' => 'Name',
			'start' => 'Start',
			'end' => 'End',
			'country_id' => 'Country',
			'state_id' => 'State',
			'together_flag' => 'Together',
			'priority' => 'Priority',
			'calculation_method' => 'Calculation Method',
			'amount' => 'Amount',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'name' => array(array('not_empty')),
			'calculation_method' => array(array('not_empty')),
			'amount' => array(array('not_empty')),
		);
	}

	/**
	 * Filter definitions, run everytime a field is set.
	 *
	 * @return  array
	 */
	public function filters() {
		return array(
			'name' => array(array('trim')),
		);
	}
} // class