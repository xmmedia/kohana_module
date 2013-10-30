<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_HTMLBasic extends ORM_HTML {
	/**
	* Does the same thing as XM_ORM_HTML::edit() but adds the class textarea_html to every input
	*/
	public static function edit($column_name, $html_name, $body, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		return XM_ORM_HTML::edit($column_name, $html_name, $body, $attributes, $options, $orm_model);
	}
}
