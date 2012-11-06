<?php defined('SYSPATH') or die ('No direct script access.');

class Model_XM_User extends Model_cl4_User {
	/**
	 * Returns the user's name: first, last.
	 *
	 * @param  boolean  $last_first  If true, it will be returned as last, first.
	 * @return string
	 */
	public function name($last_first = FALSE) {
		if ($last_first) {
			return UTF8::trim($this->last_name . ', ' . $this->first_name);
		} else {
			return UTF8::trim($this->first_name . ' ' . $this->last_name);
		}
	}
}