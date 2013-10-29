<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default private Controller class.
 * Some of the functionality is required by XM and other modules.
 */
class Controller_XM_Private extends Controller_Base {
	/**
	 * Sets up the template script var, add's jquery, jquery ui, jquery outside, xm.js, ajax.js, and base.js.
	 * If not in dev, private.min.js will be added instead of jquery outside, xm, ajax and base.
	 *
	 * @return  Controller_Base
	 */
	public function add_template_js() {
		$this->add_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js')
			->add_script('jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js');
		if (DEBUG_FLAG) {
			$this->add_script('xm_debug', 'xm/js/debug.js');
		}
		if (XM::is_dev()) {
			$this->add_script('jquery_outside', 'js/jquery.outside.min.js')
				->add_script('xm', 'xm/js/xm.js')
				->add_script('xm_ajax', 'xm/js/ajax.js')
				->add_script('base', 'js/base.js')
				->add_script('private', 'js/private.js');
		} else {
			$this->add_script('private', 'js/private.min.js');
		}

		return $this;
	} // function add_template_js
}