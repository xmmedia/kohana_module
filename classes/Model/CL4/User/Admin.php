<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * extends Model_User, mostly adds the password and confirm_password functionality
 */
class Model_CL4_User_Admin extends Model_User {
	protected function _initialize() {
		$this->_table_columns['password']['field_type'] = 'Password';
		$this->_table_columns['password']['list_flag'] = FALSE;
		$this->_table_columns['password']['edit_flag'] = TRUE;

		// add the password column, a copy of the password field
		$this->_table_columns['password_confirm'] = $this->_table_columns['password'];
		$this->_display_order[45] = 'password_confirm';

		// display the group field
		$this->_has_many['group']['edit_flag'] = TRUE;
		$this->_has_many['group']['view_flag'] = TRUE;

		parent::_initialize();
	} // function _initialize

	public function rules() {
		$rules = parent::rules();

		// remove the password rules as custom ones are added in save()
		unset($rules['password']);

		return $rules;
	} // function rules

	public function filters() {
		$filters = parent::filters();

		unset($filters['password'], $filters['password_confirm']);

		return $filters;
	} // function filters

	/**
	 * Updates or Creates the record depending on loaded()
	 * Adds validation for the password and password_confirm, using rules from Model_User
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @return ORM
	 */
	public function save(Validation $validation = NULL) {
		// if there is a changed password_confirm field, remove it as it can't be saved
		if (array_key_exists('password_confirm', $this->_changed)) unset($this->_changed['password_confirm']);

		// only do the validation if the password has been sent
		if ( ! empty($this->password)) {
			// if there is no validation object passed, then create one, otherwise use the passed on
			if ($validation === NULL) {
				$validation = Validation::factory($this->_object);
			}
			$labels = $this->labels();
			$rules = parent::rules(); // get the parent rules, because the rules are modified within this Model

			// add the validation labels and rules
			$validation->label('password', $labels['password'])
				->label('password_confirm', $labels['password_confirm'])
				->rules('password', $rules['password'])
				->rule('password_confirm', 'matches', array(':validation', 'password', 'password_confirm'));

			// since the password was received, hash it (the data in validation will be kept separate)
			$this->password = $this->hash_password($this->password);

		} else if (array_key_exists('password', $this->_changed)) {
			 unset($this->_changed['password']);
		}

		return parent::save($validation);
	} // function save

	/**
	 * Same as parent _build_select, but, if set, the password_confirm column is not added to the select statement
	 * (as it's not actually in the db).
	 * This is a hack that we'll need to determine a better way.
	 *
	 * @return  array  Columns to select
	 */
	protected function _build_select() {
		$columns = array();

		if (isset($this->_table_columns['password_confirm'])) {
			$password_confirm_column = $this->_table_columns['password_confirm'];
			unset($this->_table_columns['password_confirm']);
		}

		foreach ($this->_table_columns as $column => $_) {
			$columns[] = array($this->_object_name . '.' . $column, $column);
		}

		if (isset($password_confirm_column)) {
			$this->_table_columns['password_confirm'] = $password_confirm_column;
		}

		return $columns;
	}
} // class