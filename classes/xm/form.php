<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Form extends cl4_Form {
	public static function month($name, $selected, $attributes = NULL, $options = array()) {
		$options += array(
			'include_month_number' => FALSE,
		);

		$months = array();
		for ($i = 1; $i <= 12; $i ++) {
			$months[$i] = ($options['include_month_number'] ? $i . ' - ' : '') . date('F', Date::MONTH * $i - (Date::DAY * 2));
		}

		return Form::select($name, $months, $selected, $attributes, $options);
	} // function month

	public static function weekday($name, $selected, $attributes = NULL, $options = array()) {
		$options += array(
			'begins_on_sunday' => TRUE,
		);

		$days = array(
			2 => __('Monday'),
			3 => __('Tuesday'),
			4 => __('Wednesday'),
			5 => __('Thursday'),
			6 => __('Friday'),
			7 => __('Saturday'),
		);
		if ($options['begins_on_sunday']) {
			$days = Arr::unshift($days, 1, __('Sunday'));
		} else {
			$days[1] = __('Sunday');
		}

		return Form::select($name, $days, $selected, $attributes, $options);
	} // function weekday

	public static function user_select($name, $selected, $attributes = NULL, $options = array()) {
		$users = DB::select('id', array(DB::expr("CONCAT_WS(' ', first_name, last_name)"), 'user_name'))
			->from('user')
			->where_expiry()
			->execute()
			->as_array('id', 'user_name');

		return Form::select($name, $users, $selected, $attributes, $options);
	} // function user_select
} // class XM_Form