<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Tree extends Controller_Base {
	public $auth_required = TRUE;

	public function before() {
		parent::before();

		$this->add_admin_css();

		$this->template->styles['xm/css/tree.css'] = 'screen';
		$this->template->scripts['tree'] = 'xm/js/tree.js';
	}

	/**
	 * Action: index
	 *
	 * @return void
	 */
	public function action_index() {
		try {
			$this->template->body_html = View::factory('tree/index');
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
		}
	} // function action_index
}