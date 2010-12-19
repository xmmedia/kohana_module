<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Base extends Controller_cl4_Base {
	public $allowed_languages = array('en-ca');

	public function set_template_meta() {
		// an array of meta tags where the key is the name and value is the content
		if (empty($this->template->meta_tags)) $this->template->meta_tags = array();
		if ( ! isset($this->template->meta_tags['description'])) $this->template->meta_tags['description'] = '';
		if ( ! isset($this->template->meta_tags['keywords'])) $this->template->meta_tags['keywords'] = '';
		if ( ! isset($this->template->meta_tags['author'])) $this->template->meta_tags['author'] = '';
		if ( ! isset($this->template->meta_tags['viewport'])) $this->template->meta_tags['viewport'] = 'maximum-scale=1.0;';
	} // function set_template_meta
}