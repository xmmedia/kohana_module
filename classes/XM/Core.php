<?php defined('SYSPATH') OR die('No direct access allowed.');

class XM_Core extends Kohana_Core {
	/**
	* Returns the email address the error messages should be sent to.
	*
	* @return  string
	*/
	public static function get_error_email() {
		return Kohana::$config->load('error_admin.error_email');
	}

	/**
	* Returns TRUE if we are currently in development
	*
	* @return  bool
	*/
	public static function is_dev() {
		return (Kohana::DEVELOPMENT === Kohana::$environment);
	}

	/**
	* Returns TRUE if we are currently in production
	*
	* @return  bool
	*/
	public static function is_prod() {
		return (Kohana::PRODUCTION === Kohana::$environment);
	}

	/**
	* Returns TRUE if we are currently in staging
	*
	* @return  bool
	*/
	public static function is_staging() {
		return (Kohana::STAGING === Kohana::$environment);
	}

	/**
	* Returns TRUE if we are currently in testing
	*
	* @return  bool
	*/
	public static function is_testing() {
		return (Kohana::TESTING === Kohana::$environment);
	}

	/**
	 * Check for a parameter with the given key in the request data, POST overrides Route Parm overrides GET.
	 * Also applies Security::xss_clean()
	 * If the value is NULL and $type is NULL then NULL will be returned
	 *
	 * @param  string  the key of the paramter
	 * @param  mixed  the default value
	 * @param  string  used for type casting, can be 'int', 'string' or 'array'
	 * @return  mixed  the value of the parameter, or $default, or null
	 */
	public static function get_param($key, $default = NULL, $type = NULL) {
		// look in POST
		$value = Arr::get($_POST, $key);
		// check route parms; only look for it if the value was not set in POST
		if (empty($value)) {
			// controller and action are special cases
			if ($key == 'controller') {
				$value = Request::current()->controller();
			} else if ($key == 'action') {
				$value = Request::current()->action();
			} else {
				$value = Request::current()->param($key);
			} // if
		} // if
		// check for GET; only look for it if the value was not set in POST or the Route (Request)
		if (empty($value)) $value = Arr::get($_GET, $key, $default);

		return XM::clean_param($value, $type);
	} // function get_param

	/**
	* Returns the value from the POST or GET based on the array keys, if it exists
	* If the value is NULL and $type is NULL then NULL will be returned
	*
	* @param  array  $array_keys array keys to the location in the request
	* @param  mixed  the default value if nothing is found
	* @param  string  used for type casting, can be 'int', 'string' or 'array'
	* @return  mixed  the value of the parameter, or $default, or null
	*/
	public static function get_param_array($array_keys, $default = NULL, $type = NULL) {
		// determine the path to the file
		$path = implode('.', $array_keys);

		// look in post and if it's not there, look in get
		$value = Arr::path($_POST, $path);
		if (empty($value)) Arr::path($_GET, $path, $default);

		return XM::clean_param($value, $type);
	} // function get_param_array

	/**
	* Cleans the value using xss_clean and optionally casts it to a certain type
	*
	* @param  mixed  $value  the value to be cleaned
	* @param  string  $type  used for type casting, can be 'int', 'string', 'bool' or 'array'
	* @return  mixed  the cleaned value
	*/
	public static function clean_param($value, $type = NULL) {
		// cast the type if one is specified
		switch($type) {
			case 'int' :
				$cleaned_value = (int) $value;
				break;
			case 'array' :
				$cleaned_value = (array) $value;
				break;
			case 'string' :
				$cleaned_value = (string) $value;
				break;
			case 'bool' :
				$cleaned_value = (bool) $value;
				break;
			default :
				$cleaned_value = $value;
		} // switch

		return $cleaned_value;
	} // function clean_param

	/**
	* WARNING: right now this just returns the table names as an array of table_name => table_name
	* return an array containing all of the object names in the given project
	*
	* todo: make this work for objects, need object meta data -> file?  or auto-load?  expensive and slow
	*
	* @param mixed $just_tables	this will return a list of database tables instead (with underscores removed)
	*/
	public static function get_object_list($db_group = NULL, $just_tables = false) {
		$data = array();

		if ($just_tables) {
			$db = ! empty($db_group) ? Database::instance($db_group) : Database::instance();
			$data = str_replace('_', '', $db->list_tables());
		} else {
			Message::add('Error, could not generate object list.  This option is not yet supported in get_object_list', Message::$error);
			//todo: code this using
			// $file_list = kohana::list_files('classes/model');
			// todo: grab keys, strip off '/classes/model/' and php and add _ for /'s, etc.
		} // if

		// make return array use the values as keys, useful for select generation
		$return_data = array();
		foreach ($data as $object_name) {
			$return_data[$object_name] = $object_name;
		} // foreach

		return $return_data;
	} // function

	/**
	* create a slug from a phrase (remove spaces, secial characterse, etc.)
	*
	* @param mixed $phrase
	* @param mixed $maxLength
	* @return mixed
	*/
	public static function make_slug($phrase) {
		// replace non letter or digits by -
		$phrase = preg_replace('~[^\\pL\d]+~u', '-', $phrase);

		// trim
		$phrase = trim($phrase, '-');

		// transliterate
		$phrase = iconv('utf-8', 'us-ascii//TRANSLIT', $phrase);

		// lowercase
		$phrase = strtolower($phrase);

		// remove unwanted characters
		$phrase = preg_replace('~[^-\w]+~', '', $phrase);

		if (empty($phrase)) {
			return 'n-a';
		}

		return $phrase;
	} // function

	/**
	* prepare some textarea content for display
	*
	* @param mixed $content
	* @return mixed
	*
	* @todo this should not be in the library; replacing quotes and dashes (em or en?) is not something most people would want to do and there is very little need for it
	*/
	public static function format_textarea_for_html($content) {
		$formatted_content = nl2br($content);

		// replace 's with proper apostrophe
		$formatted_content = str_replace("'s", "&rsquo;s", $formatted_content);

		// replace - with proper character
		$formatted_content = str_replace(" - ", " – ", $formatted_content);

		return $formatted_content;
	} // function

	/**
	 * Generates a nicer looking name by replacing _ (underscores) with spaces and making the first letter of words upper case.
	 *
	 * @param   string  $name  The string replace the underscores in.
	 *
	 * @return  string
	 */
	public static function underscores_to_words($name) {
		return ucwords(str_replace('_', ' ', $name));
	}

	/**
	* Recursively translates all the values and optionally the keys of an array
	*
	* @param array $array The array to translate
	* @param bool $key Set to TRUE if you want to keys to be translated as well
	* @return array
	*/
	public static function translate_array($array, $key = FALSE) {
		foreach ($array as $key => $value) {
			if ($key) {
				if (is_array($value)) {
					$array[__($key)] = XM::translate_array($value, $key);
				} else {
					$array[__($key)] = __($value);
				}
			} else {
				if (is_array($value)) {
					$array[$key] = XM::translate_array($value, $key);
				} else {
					$array[$key] = __($value);
				}
			} // if
		} // foreach

		return $array;
	} // function

	/**
	* Used in Form::phone(), ORM_Phone and XM::format_phone() to break apart the phone number stored in the database as a string
	* Returns an array of the different phone number parts
	*
	* @param string $value
	*/
	public static function parse_phone_value($value) {
		if ( ! empty($value)) {
			// convert the data in to an array
			$default_data = explode('-', $value, 5);
		} else {
			$default_data = array();
		} // if

		return array(
			'country_code' => (isset($default_data[0]) ? $default_data[0] : NULL),
			'area_code' => (isset($default_data[1]) ? $default_data[1] : NULL),
			'exchange' => (isset($default_data[2]) ? $default_data[2] : NULL),
			'line' => (isset($default_data[3]) ? $default_data[3] : NULL),
			'extension' => (isset($default_data[4]) ? $default_data[4] : NULL),
		);
	} // function

	/**
	* Returns a formatted phone number
	* For use with Form::phone()
	* If a string is passed it will be parsed with XM::parse_phone_value() first
	*
	* @param mixed $phone
	* @return string
	*/
	public static function format_phone($phone) {
		if ( ! is_array($phone)) {
			// assume that we've been passed the string that's in the database and try to get it's parts
			$phone = XM::parse_phone_value($phone);
		}

		$formatted_phone = '';

		if ( ! empty($phone['country_code'])) $formatted_phone .= '+ ' . $phone['country_code'];
		// add the area code
		if ( ! empty($phone['area_code']))    $formatted_phone .= ' (' . $phone['area_code'] . ')';
		// add the exchange field
		if ( ! empty($phone['exchange']))     $formatted_phone .= ' ' . $phone['exchange'];
		// add the line field
		if ( ! empty($phone['line']))         $formatted_phone .= '-' . $phone['line'];
		// add the extension field
		if ( ! empty($phone['extension']))    $formatted_phone .= ' ' . __('ext.') . ' ' . $phone['extension'];

		return UTF8::trim($formatted_phone);
	} // function format_phone

	public static function psr0($lower_case) {
		return str_replace(' ', '_', ucwords(str_replace('_', ' ', $lower_case)));
	}
} // class