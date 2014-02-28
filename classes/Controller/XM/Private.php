<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default private Controller class.
 * Some of the functionality is required by XM and other modules.
 */
class Controller_XM_Private extends Controller_Base {
	/**
	 * Controls access for the whole controller.
	 * If the entire controller REQUIRES that the user be logged in, set this to TRUE.
	 * If some or all of the controller DOES NOT need to be logged in, set to this FALSE; to control which actions require authentication or a specific permission, us the $secure_actions array.
	 * By default, all Private Controllers are auth required.
	 */
	public $auth_required = TRUE;

	/**
	 * Called before the action.
	 * Does everything else in the parent before()'s and also adds the admin CSS.
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->add_style('private', 'css/private.css');
		}
	}

	/**
	 * Adds `js/private.min.js` in addition to the JS added in `Controller_Base`.
	 *
	 * @return  Controller_Private
	 */
	public function add_template_js() {
		parent::add_template_js();

		$this->add_script('private', 'js/private.min.js');

		return $this;
	}
}