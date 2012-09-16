<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_discount_product`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Discount_Product extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_discount_product';
	public $_table_name_display = 'Cart - Discount Product'; // cl4 specific

	// relationships
	//protected $_has_one = array();
	//protected $_has_many = array();
	protected $_belongs_to = array(
		'cart_discount' => array(
			'model' => 'cart_discount',
			'foreign_key' => 'cart_discount_id',
		),
		'cart_product' => array(
			'model' => 'cart_product',
			'foreign_key' => 'cart_product_id',
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
		'cart_discount_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'cart_discount',
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
			'cart_discount_id' => 'Discount',
			'cart_product_id' => 'Product',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'cart_discount_id' => array(array('selected')),
			'cart_product_id' => array(array('selected')),
		);
	}
} // class