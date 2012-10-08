<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Valid extends cl4_Valid {
	/**
	 * Checks if the value of a field is a valid date. The date must be in the format of "YYYY-MM-DD".
	 *
	 * @param   string  $value      value
	 * @return  boolean
	 */
	public static function valid_date($value) {
		$date = explode('-', $value);

		if (count($date) == 3 && checkdate($date[1], $date[2], $date[0])) {
			return TRUE;
		}

		return FALSE;
	}
}