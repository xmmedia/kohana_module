<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_UserSelect extends ORM_Select {
	public static function edit($column_name, $html_name, $selected, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		$default_options = array(
			'select_one' => FALSE,
			'select_all' => FALSE,
			'select_none' => TRUE,
		);
		$options = array_merge($default_options, $options);

		return Form::user_select($html_name, $selected, $attributes, $options);
	} // function edit

	public static function search($column_name, $html_name, $selected, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		if (empty($selected)) {
			$selected = array('all');
		}

		// the default options are no select one, but add all and none
		// this will allow someone to search for anything and ones that aren't set
		$default_options = array(
			'select_one' => FALSE,
			'select_all' => TRUE,
			'select_none' => TRUE,
		);
		$options = array_merge($default_options, $options);

		if ( ! array_key_exists('multiple', $attributes)) {
			$attributes['multiple'] = TRUE;
			if (substr($html_name, -2, 2) != '[]') {
				$html_name .= '[]';
			}
		}

		return Form::user_select($html_name, $selected, $attributes, $options);
	} // function

	public static function view($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		$user_name = DB::select(array(DB::expr("CONCAT_WS(' ', first_name, last_name)"), 'user_name'))
			->from('user')
			->where('id', '=', $value)
			->where_expiry()
			->execute()
			->get('user_name');

		if ($user_name !== NULL) {
			return $user_name;
		} else {
			return NULL;
		}
	}

	public static function view_html($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		$found_value = ORM_UserSelect::view($value, $column_name, $orm_model, $options, $source);
		if ($found_value !== NULL && $found_value !== 0) {
			return ORM_Select::prepare_html(__($found_value), $options['nbsp']);
		} else if ($value > 0) {
			// the value is still > 0 but we don't know what the value is because it's not in the data
			return __(Kohana::message('cl4', 'cl4_unknown_html'));
		} else {
			// the value is not set (0 or NULL likely)
			return __(Kohana::message('cl4', 'cl4_not_set_html'));
		}
	}
} // class XM_ORM_UserSelect