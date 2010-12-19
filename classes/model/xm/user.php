<?php defined('SYSPATH') or die ('No direct script access.');

class Model_XM_User extends Model_cl4_User {
	// settings for user (from settings field)
	protected $_settings;
	// default settings
	protected $_default_settings = array();

	/**
	* Sets or retrieves a setting.
	* To use this function, a "settings" field must be in the user table.
	* To get a value, do the following:
	*
	*     $setting = $user->setting('path.to.setting');
	*
	* To set a value, do the following:
	*
	*     $user->setting('path.to.setting', $value);
	*
	* @param   string  $setting  Dot separated path to the setting
	* @param   mixed   $value    The value to set (not required when getting a setting)
	* @return  ORM     When setting a value, the function returns the object. When getting a setting, it returns the value.
	*/
	public function setting() {
		// settings have not been unserialized yet
		if ($this->_settings === NULL) {
			$this->_settings = unserialize($this->settings);

			if (empty($this->_settings)) {
				$this->_settings = array();
			}
		} // if

		if (func_num_args() == 2) {
			list($setting, $value) = func_get_args();

			$setting_keys = explode('.', $setting);
			$key_count = count($setting_keys);

			if ($key_count == 1) {
				$this->_settings[$setting] = $value;

				return $this;

			} else if ($key_count > 1) {
				$eval_string = '';

				foreach ($setting_keys as $key) {
					$eval_string .= '["' . str_replace('"', '\"', $key) . '"]';
				}

				eval('$this->_settings' . $eval_string . ' = "' . str_replace('"', '\"', $value) . '";');
			} // if

			return $this;

		} else {
			list($setting) = func_get_args();

// @todo figure out how this works with values that are null when found, specifically when it's not an array that's found
			$found_settings = Arr::path($this->_settings, $setting);
			$default_settings = Arr::path($this->_default_settings, $setting);

			if (is_array($default_settings)) {
				if (is_array($found_settings)) {
					return Arr::merge($default_settings, $found_settings);
				} else {
					return $default_settings;
				}
			} else {
				return $found_settings;
			}
		}
	} // function setting

	/**
	 * Allows serialization of only the object data and state, to prevent
	 * "stale" objects being unserialized, which also requires less memory.
	 * Also serializes and saves the user's settings.
	 *
	 * The same as Kohana::__sleep() but we also include the _options array
	 *
	 * @return  array
	 */
	public function __sleep() {
		// serialize and save settings
		if ( ! empty($this->_settings)) {
			try {
				$this->settings = serialize($this->_settings);
				$this->save();
			} catch (Exception $e) {
				cl4::exception_handler($e);
			}
		}

		return parent::__sleep();
	}
}