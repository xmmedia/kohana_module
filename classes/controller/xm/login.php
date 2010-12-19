<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Login extends Controller_cl4_Login {
	public function before() {
		parent::before();

		$this->add_admin_css();
	} // function before

	/**
	* Adds the CSS for cl4admin
	*/
	protected function add_admin_css() {
		if ($this->auto_render) {
			$this->template->styles['css/admin.css'] = 'screen';
		}
	} // function add_admin_css
}