<?php defined('SYSPATH') or die ('No direct script access.');

class Model_CL4_User_Password extends Model_User {
	protected function _initialize() {
		$this->_table_columns['id']['edit_flag'] = FALSE;
		$this->_table_columns['active_flag']['edit_flag'] = FALSE;
		$this->_table_columns['username']['edit_flag'] = FALSE;
		$this->_table_columns['first_name']['edit_flag'] = FALSE;
		$this->_table_columns['last_name']['edit_flag'] = FALSE;
		$this->_table_columns['login_count']['edit_flag'] = FALSE;
		$this->_table_columns['failed_login_count']['edit_flag'] = FALSE;
		$this->_table_columns['last_failed_login']['edit_flag'] = FALSE;
		$this->_table_columns['reset_token']['edit_flag'] = FALSE;

		parent::_initialize();
	}
} // class