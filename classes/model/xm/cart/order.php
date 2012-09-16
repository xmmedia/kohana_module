<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_order`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Order extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_order';
	protected $_primary_val = 'invoice'; // default: name (column used as primary value)
	public $_table_name_display = 'Cart - Order'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'date_modified' => 'DESC',
	);

	// relationships
	protected $_has_many = array(
		'cart_order_additional_charge' => array(
			'model' => 'cart_order_additional_charge',
			'foreign_key' => 'cart_order_id',
		),

		// discounts
		'cart_order_discount' => array(
			'model' => 'cart_order_discount',
			'foreign_key' => 'cart_order_id',
		),
		'cart_discount' => array(
			'model' => 'cart_discount',
			'through' => 'cart_order_discount',
			'foreign_key' => 'cart_order_id',
			'far_key' => 'cart_discount_id',
		),

		'cart_order_log' => array(
			'model' => 'cart_order_log',
			'foreign_key' => 'cart_order_id',
		),
		'cart_order_payment' => array(
			'model' => 'cart_order_payment',
			'foreign_key' => 'cart_order_id',
		),

		// products
		'cart_order_product' => array(
			'model' => 'cart_order_product',
			'foreign_key' => 'cart_order_id',
		),
		'cart_product' => array(
			'model' => 'cart_product',
			'through' => 'cart_order_product',
			'foreign_key' => 'cart_order_id',
			'far_key' => 'cart_product_id',
		),

		// shipping
		'cart_order_shipping' => array(
			'model' => 'cart_order_shipping',
			'foreign_key' => 'cart_order_id',
		),
		'cart_shipping' => array(
			'model' => 'cart_shipping',
			'through' => 'cart_order_shipping',
			'foreign_key' => 'cart_order_id',
			'far_key' => 'cart_shipping_id',
		),

		// taxes
		'cart_order_tax' => array(
			'model' => 'cart_order_tax',
			'foreign_key' => 'cart_order_id',
		),
		'cart_tax' => array(
			'model' => 'cart_tax',
			'through' => 'cart_order_tax',
			'foreign_key' => 'cart_order_id',
			'far_key' => 'cart_tax_id',
		),
	);
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
		),
		'country' => array(
			'model' => 'country',
			'foreign_key' => 'country_id',
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
		'date_created' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_modified' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_payment' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_received' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_completed' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_required' => array(
			'field_type' => 'date',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_shipped' => array(
			'field_type' => 'datetime',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'user_id' => array(
			'field_type' => 'select',
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
		'sub_total' => array(
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
		'grand_total' => array(
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
		'exchange_rate' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 12,
				'size' => 12,
				'class' => 'numeric',
			),
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
		'invoice' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 10,
				'size' => 10,
			),
		),
		'internal_order_num' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 15,
				'size' => 15,
			),
		),
		'encrypted_code' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 256,
			),
		),
		'status_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'array',
					'data' => array(
						1 => 'Unpaid / New Order',
						2 => 'Complete',
						3 => 'Payment in Progress',
						4 => 'Paid',
						5 => 'Shipped',
						6 => 'Submitted / Waiting for Payment',
					),
				),
			),
		),
		'po_number' => array(
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
		'order_note' => array(
			'field_type' => 'textarea',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'user_address_loaded_flag' => array(
			'field_type' => 'checkbox',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'email' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 200,
			),
		),
		'shipping_first_name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'shipping_last_name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'shipping_company' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'shipping_address1' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'shipping_address2' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'shipping_city' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'shipping_state_id' => array(
			'field_type' => 'select',
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
		'shipping_state' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'shipping_postal_code' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 10,
				'size' => 10,
			),
		),
		'shipping_country_id' => array(
			'field_type' => 'select',
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
		'shipping_phone' => array(
			'field_type' => 'phone',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'same_as_shipping_flag' => array(
			'field_type' => 'checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'billing_first_name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'billing_last_name' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'billing_company' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'billing_address1' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'billing_address2' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 255,
			),
		),
		'billing_city' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'billing_state_id' => array(
			'field_type' => 'select',
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
		'billing_state' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 150,
			),
		),
		'billing_postal_code' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 10,
				'size' => 10,
			),
		),
		'billing_country_id' => array(
			'field_type' => 'select',
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
		'billing_phone' => array(
			'field_type' => 'phone',
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
	protected $_created_column = array('column' => 'date_created', 'format' => 'Y-m-j H:i:s');

	/**
	 * @var  array  $_updated_column  The date and time this row was updated.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_updated_column = array('column' => 'date_modified', 'format' => TRUE);

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
			'date_created' => 'Date Created',
			'date_modified' => 'Date Modified',
			'date_payment' => 'Date Payment',
			'date_received' => 'Date Received',
			'date_completed' => 'Date Completed',
			'date_required' => 'Date Required',
			'date_shipped' => 'Date Shipped',
			'user_id' => 'User',
			'sub_total' => 'Sub Total',
			'grand_total' => 'Grand Total',
			'exchange_rate' => 'Exchange Rate',
			'country_id' => 'Country',
			'invoice' => 'Invoice',
			'internal_order_num' => 'Internal Order Number',
			'encrypted_code' => 'Encrypted Code',
			'status_id' => 'Status',
			'po_number' => 'PO Number',
			'order_note' => 'Order Note',
			'user_address_loaded_flag' => 'User Address Loaded',
			'email' => 'Email',
			'shipping_first_name' => 'Shipping First Name',
			'shipping_last_name' => 'Shipping Last Name',
			'shipping_company' => 'Shipping Company',
			'shipping_address1' => 'Shipping Address 1',
			'shipping_address2' => 'Shipping Address 2',
			'shipping_city' => 'Shipping City',
			'shipping_state_id' => 'Shipping State',
			'shipping_state' => 'Shipping State',
			'shipping_postal_code' => 'Shipping Postal Code',
			'shipping_country_id' => 'Shipping Country',
			'shipping_phone' => 'Shipping Phone',
			'same_as_shipping_flag' => 'Same As Shipping',
			'billing_first_name' => 'Billing First Name',
			'billing_last_name' => 'Billing Last Name',
			'billing_company' => 'Billing Company',
			'billing_address1' => 'Billing Address 1',
			'billing_address2' => 'Billing Address 2',
			'billing_city' => 'Billing City',
			'billing_state_id' => 'Billing State',
			'billing_state' => 'Billing State',
			'billing_postal_code' => 'Billing Postal Code',
			'billing_country_id' => 'Billing Country',
			'billing_phone' => 'Billing Phone',
		);
	}

	/**
	 * Filter definitions, run everytime a field is set.
	 *
	 * @return  array
	 */
	public function filters() {
		return array(
			'invoice' => array(array('trim')),
		);
	}
} // class