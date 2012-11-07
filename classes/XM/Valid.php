<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Valid extends CL4_Valid {
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
}