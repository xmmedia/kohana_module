<?php defined('SYSPATH') or die ('No direct script access.');

class Model_CL4_User_Profile extends Model_User {
	protected function _initialize() {
		$this->_table_columns['id']['edit_flag'] = FALSE;
		$this->_table_columns['active_flag']['edit_flag'] = FALSE;
		$this->_table_columns['password']['edit_flag'] = FALSE;
		$this->_table_columns['login_count']['edit_flag'] = FALSE;
		$this->_table_columns['last_login']['edit_flag'] = FALSE;
		$this->_table_columns['failed_login_count']['edit_flag'] = FALSE;
		$this->_table_columns['last_failed_login']['edit_flag'] = FALSE;
		$this->_table_columns['reset_token']['edit_flag'] = FALSE;
		$this->_table_columns['force_update_profile_flag']['edit_flag'] = FALSE;
		$this->_table_columns['force_update_password_flag']['edit_flag'] = FALSE;

		parent::_initialize();
	} // function _initialize

	public function rules() {
		$rules = parent::rules();

		// remove the rules for password (the field is not ediable, see _initialize())
		unset($rules['password']);

		return $rules;
	} // function rules
} // class