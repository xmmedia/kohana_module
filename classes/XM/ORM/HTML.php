<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_HTML extends XM_ORM_HTML {
	/**
	* Does the same thing as XM_ORM_HTML::edit() but adds the class textarea_html to every input
	*/
	public static function edit($column_name, $html_name, $body, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		$attributes = HTML::set_class_attribute($attributes, 'textarea_html');
		return Form::html($html_name, $body, $attributes);
	}

	/**
	* The only difference between this and ORM_TextArea::view_html() is that this won't be encoded before being returned
	* thus HTML will end up as HTML
	*
	* @param mixed $value
	* @param mixed $column_name
	* @param ORM $orm_model
	* @param mixed $options
	* @param mixed $source
	* @return string
	*/
	public static function view_html($value, $column_name, ORM $orm_model = NULL, array $options = array(), $source = NULL) {
		return ORM_TextArea::view($value, $column_name, $orm_model, $options);
	} // function
}