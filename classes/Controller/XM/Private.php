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
	} // function before

	/**
	 * Sets up the template script var, add's jquery, jquery ui, jquery outside, xm.js, ajax.js, and base.js.
	 * If not in dev, private.min.js will be added instead of jquery outside, xm, ajax and base.
	 *
	 * @return  Controller_Base
	 */
	public function add_template_js() {
		$this->add_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js')
			->add_script('jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');

		if (XM::is_dev()) {
			$this->add_script('xm_debug', 'xm/js/debug.js');
		}

		$this->add_script('base', 'js/base.min.js')
			->add_script('private', 'js/private.min.js');

		return $this;
	} // function add_template_js
}