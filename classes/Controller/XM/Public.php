<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Public controller for public pages.
 */
class Controller_XM_Public extends Controller_Base {
	/**
	 * The template to use. The string is replaced with the View in before().
	 * @var  View
	 */
	public $template = 'public/template';

	/**
	 * Called before the action.
	 * Does everything else in the parent before()'s and also adds the public CSS.
	 */
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->add_style('public', 'css/public.css');
		}
	} // function before
}