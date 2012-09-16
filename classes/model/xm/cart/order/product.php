<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_order_product`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Order_Product extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_order_product';
	//protected $_primary_val = 'name'; // default: name (column used as primary value)
	public $_table_name_display = 'Cart - Order Product'; // cl4 specific

	// default sorting
	//protected $_sorting = array();

	// relationships
	//protected $_has_one = array();
	//protected $_has_many = array();
	//protected $_belongs_to = array();

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
		'date_added' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_modified' => array(
			'field_type' => 'hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_removed' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'cart_order_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'cart_order',
				),
			),
		),
		'cart_product_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'cart_product',
				),
			),
		),
		'quantity' => array(
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
		'unit_price' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 11,
				'size' => 11,
			),
		),
		'data' => array(
			'field_type' => 'textarea',
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
	//protected $_created_column = array('column' => 'date_created', 'format' => 'Y-m-j H:i:s');

	/**
	 * @var  array  $_updated_column  The date and time this row was updated.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_updated_column = array('column' => 'date_modified', 'format' => TRUE);

	/**
	 * @var  array  $_expires_column  The time this row expires and is no longer returned in standard searches.
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	/*
	protected $_expires_column = array(
		'column' 	=> 'expiry_date',
		'default'	=> 0,
	);
	*/

	/**
	 * @var  array  $_display_order  The order to display columns in, if different from as listed in $_table_columns.
	 * Columns not listed here will be added beneath these columns, in the order they are listed in $_table_columns.
	 */
	/*
	protected $_display_order = array(
		'id',
		'date_added',
		'date_modified',
		'date_removed',
		'cart_order_id',
		'cart_product_id',
		'quantity',
		'unit_price',
	);
	*/

	/**
	* Labels for columns
	*
	* @return  array
	*/
	public function labels() {
		return array(
			'id' => 'ID',
			'date_added' => 'Date Added',
			'date_modified' => 'Date Modified',
			'date_removed' => 'Date Removed',
			'cart_order_id' => 'Order',
			'cart_product_id' => 'Product',
			'quantity' => 'Quantity',
			'unit_price' => 'Unit Price',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	/*
	public function rules() {
		return array();
	}
	*/

	/**
	* Filter definitions, run everytime a field is set
	*
	* @return  array
	*/
	/*
	public function filters() {
		return array(TRUE => array(array('trim')),);
	}
	*/
} // class