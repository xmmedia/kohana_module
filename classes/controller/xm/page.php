<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Page extends Controller_cl4_Page {
	public function add_template_styles() {
		parent::add_template_styles();
		$this->template->styles['css/public.css'] = 'screen';
	} // function add_template_styles
}