<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_Month extends ORM_Select {
	public static function edit($column_name, $html_name, $selected, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		// the default options are everything off, except for the None
		// this will ensure there is the ability to set something to none or not set
		$default_options = array(
			'select_one' => FALSE,
			'select_all' => FALSE,
			'select_none' => TRUE,
		);
		$options = array_merge($default_options, $options);

		return Form::month($html_name, $selected, $attributes, $options);
	}

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

		return Form::month($html_name, $selected, $attributes, $options);
	} // function
}