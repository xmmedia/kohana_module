<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_order_log`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Order_Log extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_order_log';
	public $_table_name_display = 'Cart - Order Log'; // cl4 specific
	protected $_log = FALSE; // don't log changes to the log

	// default sorting
	protected $_sorting = array(
		'event_timestamp' => 'DESC',
	);

	// relationships
	protected $_belongs_to = array(
		'cart_order' => array(
			'model' => 'cart_order',
			'foreign_key' => 'cart_order_id',
		),
		'cart_product' => array(
			'model' => 'cart_product',
			'foreign_key' => 'cart_product_id',
		),
		'user' => array(
			'model' => 'user',
			'foreign_key' => 'user_id',
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
		'user_id' => array(
			'field_type' => 'select',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_options' => array(
				'source' => array(
					'source' => 'model',
					'data' => 'user',
				),
			),
		),
		// set automatically with MySQL CURRENT_TIMESTAMP
		'event_timestamp' => array(
			'field_type' => 'datetime',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'action' => array(
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
		'data' => array(
			'field_type' => 'textarea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
	);

	/**
	* Labels for columns
	*
	* @return  array
	*/
	public function labels() {
		return array(
			'id' => 'ID',
			'cart_order_id' => 'Order',
			'user_id' => 'User',
			'event_timestamp' => 'Event Timestamp',
			'action' => 'Action',
			'data' => 'Data',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'cart_order_id' => array(array('selected')),
			'action' => array(array('not_empty')),
		);
	}
} // class