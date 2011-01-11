<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Form extends cl4_Form {
	public static function month($name, $selected, $attributes, $options) {
		$options += array(
			'include_month_number' => FALSE,
		);

		$months = array();
		for ($i = 1; $i <= 12; $i ++) {
			$months[$i] = ($options['include_month_number'] ? $i . ' - ' : '') . date('F', Date::MONTH * $i - (Date::DAY * 2));
		}

		return Form::select($name, $months, $selected, $attributes, $options);
	} // function month
} // class XM_Form