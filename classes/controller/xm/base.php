<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Base extends Controller_cl4_Base {
	/**
	* Sets up the template script var, add's modernizr, jquery, jquery ui, cl4.js and base.js if they are not already set
	*
	* @return  Controller_Base
	*/
	public function add_template_js() {
		if (empty($this->template->modernizr_path)) $this->template->modernizr_path = 'js/modernizr.min.js';

		if (empty($this->template->scripts)) $this->template->scripts = array();
		// add jquery js (for all pages, other js relies on it, so it has to be included first)
		if ( ! isset($this->template->scripts['jquery'])) $this->template->scripts['jquery'] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';
		if ( ! isset($this->template->scripts['jquery_ui'])) $this->template->scripts['jquery_ui'] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js';
		if ( ! isset($this->template->scripts['cl4'])) $this->template->scripts['cl4'] = 'cl4/js/cl4.js';
		if ( ! isset($this->template->scripts['cl4_ajax'])) $this->template->scripts['cl4_ajax'] = 'cl4/js/ajax.js';
		if ( ! isset($this->template->scripts['jquery_outside'])) $this->template->scripts['jquery_outside'] = 'js/jquery.outside.min.js';
		if ( ! isset($this->template->scripts['base'])) $this->template->scripts['base'] = 'js/base.js';

		if (empty($this->template->on_load_js)) $this->template->on_load_js = '';

		return $this;
	} // function add_template_js
}