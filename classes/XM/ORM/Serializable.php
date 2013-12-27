<?php defined('SYSPATH') OR die('No direct access allowed.');

class XM_ORM_Serializable extends ORM_TextArea {
	public static function edit($column_name, $html_name, $body, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		return Form::textarea($html_name, json_encode($body), $attributes)
			// display a little extra help regarding using JSONmate to edit
			. View::factory('xm/field_help')
				->set('mode', 'edit')
				->bind('field_html_name', $html_name)
				->set('field_help', 'To edit, paste the JSON into <a href="http://jsonmate.com/" target="_blank">JSONmate</a>.');
	}

	public static function save($post, $column_name, array $options = array(), ORM $orm_model = NULL) {
		$options += array(
			'default_value' => NULL,
		);

		$value = Arr::get($post, $column_name, $options['default_value']);

		if ($value !== NULL || $options['is_nullable']) {
			$orm_model->$column_name = json_decode($value, TRUE);
		}
	}

	public static function view_html($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		return Debug::vars($value);
	}
} // class