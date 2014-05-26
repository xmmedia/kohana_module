<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Valid extends Kohana_Valid {
	/**
	 * Checks if the value of a field is a valid date. The date must be in the format of "YYYY-MM-DD".
	 *
	 * @param   string  $value      value
	 * @return  boolean
	 */
	public static function valid_date($value) {
		$date = explode('-', $value);

		if (count($date) == 3 && checkdate(intval($date[1]), intval($date[2]), intval($date[0]))) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Tests if a number is greater or equal to a value.
	 *
	 * @param   string  $number number to check
	 * @param   integer $min    minimum value
	 * @return  boolean
	 */
	public static function greater_than($number, $min) {
		return ($number >= $min);
	}

	/**
	 * Tests if the value is not in the listed array.
	 *
	 * @param   string  $value  The value to check
	 * @param   array   $array  The array to check if value is in
	 * @return  boolean
	 */
	public static function not_in_array($value, $array) {
		return ! in_array($value, (array) $array);
	}

	/**
	 * Checks if a value been selected for a field, such as radios or a checkbox.
	 *
	 * @return  boolean
	 */
	public static function selected($value) {
		return $value > 0;
	}

	/**
	 * Checks if the phone number is a valid phone number and attempts to take into account an extension.
	 * Basically checks first if the regular phone number validation works.
	 * If not, then it attempts to remove the extension (piece after last special char)
	 * and then re-checks with regular phone validation.
	 *
	 * @param   string  $number     phone number to check
	 * @param   array   $lengths
	 * @return  boolean
	 */
	public static function phone_with_ext($number, $lengths = NULL) {
		if (Valid::phone($number, $lengths)) {
			return TRUE;
		}

		$last_pos = FALSE;
		foreach (array('-', 'ext', ' ') as $char) {
			$pos = UTF8::strrpos($number, $char);
			if ($pos > 0 && $pos > $last_pos) {
				$last_pos = $pos;
			}
		}

		if ($last_pos === FALSE) {
			return FALSE;
		}

		$_number = UTF8::substr($number, 0, $last_pos);

		return Valid::phone($_number, $lengths);
	}
}