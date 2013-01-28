<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default private Controller class.
 * Some of the functionality is required by CL4 and other modules.
 */
class Controller_XM_Private extends Controller_CL4_Private {
	/**
	 * Sets up the template script var, add's jquery, jquery ui, jquery outside, cl4.js, ajax.js, and base.js.
	 * If not in dev, private.min.js will be added instead of jquery outside, cl4, ajax and base.
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
			$this->add_script('jquery_outside', 'js/jquery.outside.min.js')
				->add_script('cl4', 'cl4/js/cl4.js')
				->add_script('cl4_ajax', 'cl4/js/ajax.js')
				->add_script('base', 'js/base.js');
		} else {
			$this->add_script('private', 'js/private.min.js');
		}

		return $this;
	} // function add_template_js
}