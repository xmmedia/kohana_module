<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_discount`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Discount extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_discount';
	public $_table_name_display = 'Cart - Discount'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'cart_product' => array(
			'model' => 'cart_product',
			'through' => 'cart_discount_product',
			'foreign_key' => 'cart_discount_id',
			'far_key' => 'cart_product_id',
		),
		'cart_discount_product' => array(
			'model' => 'cart_discount_product',
			'foreign_key' => 'cart_discount_id',
		),
		'cart_order' => array(
			'model' => 'cart_order',
			'through' => 'cart_order_discount',
			'foreign_key' => 'cart_discount_id',
			'far_key' => 'cart_order_id',
		),
		'cart_order_discount' => array(
			'model' => 'cart_order_discount',
			'foreign_key' => 'cart_discount_id',
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
		'free_flag' => array(
			'field_type' => 'checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'discount_reason' => array(
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
						// 'product_count' => 'Number of Products', not implemented
						// 'weight' => 'Total Weight of Order', not implemented
						// 'order_total' => 'Order Total', not implemented
						'code' => 'Promo/Discount Code',
						// 'product' => 'Certain Product', not implemented
					),
				),
				'default_value' => 'code',
			),
		),
		'val1' => array(
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
		'val2' => array(
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
		'discount_type' => array(
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
						// 'shipping' => 'Shipping Discount', not implemented
						// 'product' => 'Product Discount', not implemented
						'order_total' => 'Order Total',
					),
				),
				'default_value' => 'order_total',
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
			'start' => 'Start',
			'end' => 'End',
			'calculation_method' => 'Calculation Method',
			'amount' => 'Amount',
			'free_flag' => 'Free',
			'discount_reason' => 'Discount Reason',
			'val1' => 'Value 1',
			'val2' => 'Value 2',
			'discount_type' => 'Discount Type',
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
			'discount_reason' => array(array('not_empty')),
			'discount_type' => array(array('not_empty')),
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
			'calculation_method' => array(array('trim')),
			'discount_reason' => array(array('trim')),
			'discount_type' => array(array('trim')),
		);
	}
} // class