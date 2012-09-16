<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_shipping`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Shipping extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_shipping';
	public $_table_name_display = 'Cart - Shipping Rate'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'display_order' => 'ASC',
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'country' => array(
			'model' => 'country',
			'through' => 'cart_shipping_location',
			'foreign_key' => 'cart_shipping_id',
			'far_key' => 'country_id',
		),
		'state' => array(
			'model' => 'state',
			'through' => 'cart_shipping_location',
			'foreign_key' => 'cart_shipping_id',
			'far_key' => 'state_id',
		),
		'cart_shipping_location' => array(
			'model' => 'cart_shipping_location',
			'foreign_key' => 'cart_shipping_id',
		),
		'cart_order' => array(
			'model' => 'cart_order',
			'through' => 'cart_order_shipping',
			'foreign_key' => 'cart_shipping_id',
			'far_key' => 'cart_order_id',
		),
		'cart_order_shipping' => array(
			'model' => 'cart_order_shipping',
			'foreign_key' => 'cart_shipping_id',
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
		'display_name' => array(
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
				'default_value' => NULL,
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
				'maxlength' => 11,
				'size' => 11,
				'class' => 'numeric',
			),
		),
		'shipping_reason_1' => array(
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
						'location' => 'Shipping Location',
						'order_total' => 'Order Total',
						'flat_rate' => 'Flat Rate (on all orders)',
						// 'weight' => 'Weight', not implemented
					),
				),
				'default_value' => NULL,
			),
		),
		'val1_1' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 20,
				'size' => 20,
			),
		),
		'val1_2' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 20,
				'size' => 20,
			),
		),
		// the second set of reason and value columns is for between values (ie, between $10 and $100)
		'shipping_reason_2' => array(
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
						'location' => 'Shipping Location',
						'order_total' => 'Order Total',
						'flat_rate' => 'Flat Rate (on all orders)',
						// 'weight' => 'Weight', not implemented
					),
				),
				'default_value' => NULL,
			),
		),
		'val2_1' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 20,
				'size' => 20,
			),
		),
		'val2_2' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 20,
				'size' => 20,
			),
		),
		'display_order' => array(
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
		'user_selectable_flag' => array(
			'field_type' => 'checkbox',
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
			'name' => 'Name',
			'display_name' => 'Display Name',
			'start' => 'Start',
			'end' => 'End',
			'calculation_method' => 'Calculation Method',
			'amount' => 'Amount',
			'shipping_reason_1' => 'Shipping Reason 1',
			'val1_1' => 'Value 1 - 1',
			'val1_2' => 'Value 1 - 2',
			'shipping_reason_2' => 'Shipping Reason 2',
			'val2_1' => 'Value 1 - 1',
			'val2_2' => 'Value 1 - 2',
			'display_order' => 'Display Order',
			'user_selectable_flag' => 'User Selectable',
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
			'display_name' => array(array('not_empty')),
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
			'display_name' => array(array('trim')),
			'calculation_method' => array(array('trim')),
		);
	}
} // class