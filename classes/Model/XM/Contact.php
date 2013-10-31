<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * This model was created using XM_ORM and should provide
 * standard Kohana ORM features in additon to xm-specific features.
 */
class Model_XM_Contact extends ORM {
	protected $_table_names_plural = FALSE;
	protected $_table_name = 'contact';
	public $_table_name_display = 'Contact'; // xm-specific

	// column definitions
	protected $_table_columns = array(
		/**
		* see http://v3.kohanaphp.com/guide/api/Database_MySQL#list_columns for all possible column attributes
		* see the modules/xm/config/xm_orm.php for a full list of xm-specific options and documentation on what the options do
		*/
		'id' => array(
			'field_type' => 'Hidden',
			'edit_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'name' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
			),
		),
		'email' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 100,
			),
		),
		'phone' => array(
			'field_type' => 'Text',
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
		'message' => array(
			'field_type' => 'TextArea',
			'list_flag' => TRUE,
			'edit_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'date_submitted' => array(
			'field_type' => 'DateTime',
			'list_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
		),
		'ip_address' => array(
			'field_type' => 'Text',
			'list_flag' => TRUE,
			'search_flag' => TRUE,
			'view_flag' => TRUE,
			'is_nullable' => FALSE,
			'field_attributes' => array(
				'maxlength' => 15,
				'size' => 15,
			),
		),
	);

	/**
	 * @var timestamp $_created_column The time this row was created.
	 *
	 * Use format => 'Y-m-j H:i:s' for DATETIMEs and format => TRUE for TIMESTAMPs.
	 */
	protected $_created_column = array(
		'column' => 'date_submitted',
		'format' => 'Y-m-j H:i:s'
	);

	/**
	 * Label definitions for validation
	 *
	 * @return array
	 */
	public function labels() {
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'email' => 'Email',
			'phone' => 'Phone',
			'message' => 'Message',
			'date_submitted' => 'Date Submitted',
			'ip_address' => 'IP Address',
		);
	}

	/**
	 * Rule definitions for validation
	 *
	 * @return array
	 */
	public function rules() {
		return array(
			'name' => array(
				array('not_empty'),
			),
			'message' => array(
				array('not_empty'),
				array('min_length', array(':value', 5)),
			),
			'email' => array(
				array(array($this, 'check_for_email_or_phone'), array(':validation', ':field')),
			),
		);
	}

	/**
	 * Filter definitions for validation
	 *
	 * @return array
	 */
	public function filters() {
		return array(
		    TRUE => array(array('trim')),
		);
	}

	/**
	 * Checks for either a valid email address or phone number.
	 * Add to the rules as:
	 *
	 *       'email' => array(
	 *               array(array($this, 'check_for_email_or_phone'), array(':validation', ':field')),
	 *       ),
	 *
	 * @param   Validation  $validate  The validation object.
	 * @param   string      $field     The field name. Not used within the method.
	 *
	 * @return  void
	 */
	public function check_for_email_or_phone(Validation $validate, $field) {
		$valid_email = Valid::email($this->email);
		$valid_phone = Valid::phone($this->phone);
		$empty_phone = ( ! empty($this->phone) || $this->phone != '----');

		if ( ! $valid_email && ! $valid_phone) {
			$validate->error('email', 'email_or_phone');
		} else {
			if ( ! $valid_email && ! empty($this->email) && ! $empty_phone && ! $valid_phone) {
				$validate->error('email', 'email');
			}
			if ( ! $valid_phone && $empty_phone && ! empty($this->email) && ! $valid_email) {
				$validate->error('phone', 'phone');
			}
		}
	}
} // class
