<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_gift_card`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Gift_Card extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_gift_card';
	protected $_primary_val = 'code'; // default: name (column used as primary value)
	public $_table_name_display = 'Cart - Gift Card'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'code' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'cart_gift_card_use' => array(
			'model' => 'cart_gift_card_use',
			'foreign_key' => 'cart_gift_card_id',
		),
		'cart_order' => array(
			'model' => 'cart_order',
			'through' => 'cart_gift_card_use',
			'foreign_key' => 'cart_gift_card_id',
			'far_key' => 'cart_order_id',
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
		'code' => array(
			'field_type' => 'text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 25,
				'size' => 25,
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
			'code' => 'Code',
			'amount' => 'Amount ($)',
		);
	}

	/**
	 * Rule definitions for validation.
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'code' => array(array('not_empty')),
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
			'code' => array(array('trim')),
		);
	}
} // class