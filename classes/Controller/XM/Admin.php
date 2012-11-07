<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Public controller for admin pages.
 */
class Controller_XM_Admin extends Controller_Base {
	/**
	 * Controls access for the whole controller.
	 * If the entire controller REQUIRES that the user be logged in, set this to TRUE.
	 * If some or all of the controller DOES NOT need to be logged in, set to this FALSE; to control which actions require authentication or a specific permission, us the $secure_actions array.
	 * By default, all Admin Controllers are auth required = 5
	 */
	public $auth_required = FALSE;

	/**
	 * Called before the action.
	 * Does everything else in the parent before()'s and also adds the admin CSS.
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->add_style('admin', 'css/admin.css');
		}
	} // function before
}