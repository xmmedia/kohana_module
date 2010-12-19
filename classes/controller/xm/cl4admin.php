<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_cl4Admin extends Controller_cl4_cl4Admin {
	/**
	* Adds the CSS for cl4admin
	*/
	protected function add_admin_css() {
		if ($this->auto_render) {
			$this->template->styles['css/admin.css'] = 'screen';
			$this->template->styles['css/dbadmin.css'] = 'screen';
		}
	} // function add_admin_css
}