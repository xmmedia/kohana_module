<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_Month extends ORM_Select {
	public static function edit($column_name, $html_name, $selected, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		// the default options are everything off, except for the None
		// this will ensure there is the ability to set something to none or not set
		$options += array(
			'select_one' => FALSE,
			'select_all' => FALSE,
			'select_none' => TRUE,
		);

		return Form::month($html_name, $selected, $attributes, $options);
	}

	public static function search($column_name, $html_name, $selected, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		if (empty($selected)) {
			$selected = array('all');
		}

		// the default options are no select one, but add all and none
		// this will allow someone to search for anything and ones that aren't set
		$options += array(
			'select_one' => FALSE,
			'select_all' => TRUE,
			'select_none' => TRUE,
		);

		if ( ! array_key_exists('multiple', $attributes)) {
			$attributes['multiple'] = TRUE;
			if (substr($html_name, -2, 2) != '[]') {
				$html_name .= '[]';
			}
		}

		return Form::month($html_name, $selected, $attributes, $options);
	} // function

	public static function view($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		return ($options['include_month_number'] ? $value . ' - ' : '') . date('F', Date::MONTH * $value - (Date::DAY * 2));
	}

	public static function view_html($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		$found_value = ORM_Month::view($value, $column_name, $orm_model, $options, $source);
		if ($found_value !== NULL && $found_value !== 0) {
			return ORM_Month::prepare_html($found_value, $options['nbsp']);
		} else if ($value > 0) {
			// the value is still > 0 but we don't know what the value is because it's not in the data
			return '<span class="cl4_unknown">' . __('unknown') . '</span>';
		} else {
			// the value is not set (0 or NULL likely)
			return '<span class="cl4_not_set">' . __('not set') . '</span>';
		}
	}
}