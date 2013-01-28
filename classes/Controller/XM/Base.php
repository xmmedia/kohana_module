<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default base Controller class.
 * Some of the functionality is required by CL4 and other modules.
 */
class Controller_XM_Base extends Controller_CL4_Base {
	/**
	 * Automatically executed before the controller action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 * Logs the request if the user is logged in.
	 * Disabled auto render if the action is in the no_auto_render_actions array.
	 * Checks to see if the site is currently unavailable and then throws a 503.
	 * Checks the login based on the auth_required and secure_actions properties.
	 * Initializes the template.
	 *
	 * @return  void
	 */
	public function before() {
		try {
			// only log the request if they're logged in
			if (Auth::instance()->logged_in()) {
				Model_Request_Log::store_request();
			}
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
		}

		parent::before();
	} // function before

	/**
	 * Sets up the template script var, add's jquery, jquery ui, and base.js if they are not already set.
	 * If not in dev, base.min.js will be added instead of base.
	 *
	 * @return  Controller_Base
	 */
	public function add_template_js() {
		$this->add_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js')
			->add_script('jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js');
		if (DEBUG_FLAG) {
			$this->add_script('xm_debug', 'xm/js/debug.js');
		}
		if (CL4::is_dev()) {
			$this->add_script('base', 'js/base.js');
		} else {
			$this->add_script('base', 'js/base.min.js');
		}

		return $this;
	} // function add_template_js
}