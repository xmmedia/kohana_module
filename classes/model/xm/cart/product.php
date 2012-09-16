<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_product`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Product extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_product';
	public $_table_name_display = 'Cart - Product'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'name' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'cart_product_property' => array(
			'model' => 'cart_product_property',
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
		'name' => array(
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
		'description' => array(
			'field_type' => 'textarea',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'cost' => array(
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
			'name' => 'Name',
			'description' => 'Description',
			'cost' => 'Cost ($)',
		);
	}

	/**
	* Rule definitions for validation
	*
	* @return  array
	*/
	public function rules() {
		return array(
			'name' => array(array('not_empty')),
			'cost' => array(array('not_empty')),
		);
	}

	/**
	* Filter definitions, run everytime a field is set
	*
	* @return  array
	*/
	public function filters() {
		return array(
			'name' => array(array('trim')),
		);
	}
} // class