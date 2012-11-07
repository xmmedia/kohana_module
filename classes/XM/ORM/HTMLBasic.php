<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM_HTMLBasic extends CL4_ORM_HTML {
	/**
	* Does the same thing as CL4_ORM_HTML::edit() but adds the class textarea_html to every input
	*/
	public static function edit($column_name, $html_name, $body, array $attributes = NULL, array $options = array(), ORM $orm_model = NULL) {
		return CL4_ORM_HTML::edit($column_name, $html_name, $body, $attributes, $options, $orm_model);
	}
}
