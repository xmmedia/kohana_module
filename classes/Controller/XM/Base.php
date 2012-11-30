<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default base Controller class.
 * Some of the functionality is required by cl4 and other modules.
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
}