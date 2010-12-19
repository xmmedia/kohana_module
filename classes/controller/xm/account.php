<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Account extends Controller_cl4_Account {
	public function before() {
		parent::before();

		$this->add_admin_css();
	} // function before
}