<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Model for `cart_property`.
 *
 * @package    XM
 * @category   Cart
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Model_XM_Cart_Property extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'cart_property';
	protected $_primary_val = 'label'; // default: name (column used as primary value)
	public $_table_name_display = 'Cart - Property'; // cl4 specific

	// default sorting
	protected $_sorting = array(
		'label' => 'ASC',
	);

	// relationships
	protected $_has_many = array(
		'cart_product' => array(
			'model' => 'cart_product',
			'through' => 'cart_product_property',
			'foreign_key' => 'cart_property_id',
			'far_key' => 'cart_product_id',
		),
		'cart_product_property' => array(
			'model' => 'cart_product_property',
			'foreign_key' => 'cart_property_id',
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
		'label' => array(
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
		'description' => array(
			'field_type' => 'text',
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 1024,
			),
		),
		'edit_flag' => array(
			'field_type' => 'checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'required_flag' => array(
			'field_type' => 'checkbox',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'field_type' => array(
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
						'radios' => 'Radios',
						'select' => 'Dropdown/Select',
						'text' => 'Text Field',
						'textarea' => 'Text Area',
					),
				),
				// need this because the values of the field are strings
				'default_value' => NULL,
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
			'label' => 'Label',
			'description' => 'Description',
			'edit_flag' => 'User Edit',
			'required_flag' => 'Required',
			'field_type' => 'Form Type',
			'data' => 'Data',
		);
	}

	/**
	 * Rule definitions for validation
	 *
	 * @return  array
	 */
	public function rules() {
		return array(
			'label' => array(array('not_empty')),
			'field_type' => array(array('not_empty')),
		);
	}

	/**
	 * Filter definitions, run everytime a field is set
	 *
	 * @return  array
	 */
	public function filters() {
		return array(
			'label' => array(array('trim')),
			'field_type' => array(array('trim')),
		);
	}
} // class