<?php defined('SYSPATH') or die ('No direct script access.');

class XM_HTML extends CL4_HTML {
	/**
	 * Creates a style sheet link element.
	 * Same as Kohana_HTML::style() but supports using //example.com/path/to/file.css and doesn't add a type="text/css"
	 *
	 *     echo HTML::style('media/css/screen.css');
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE && strpos($file, '//') !== 0) {
			// Add the base URL
			$file = URL::site(HTML::add_cache_buster($file), $protocol, $index);
		}

		// Set the stylesheet link
		$attributes['href'] = $file;

		// Set the stylesheet rel
		$attributes['rel'] = empty($attributes['rel']) ? 'stylesheet' : $attributes['rel'];

		return '<link' . HTML::attributes($attributes) . '>';
	}

	/**
	 * Creates a script link.
	 * Same as Kohana_HTML::script() but supports using //example.com/path/to/file.js and doesn't add a type="text/javascript"
	 *
	 *     echo HTML::script('media/js/jquery.min.js');
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   mixed    protocol to pass to URL::base()
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE && strpos($file, '//') !== 0) {
			// Add the base URL
			$file = URL::site(HTML::add_cache_buster($file), $protocol, $index);
		}

		// Set the script link
		$attributes['src'] = $file;

		return '<script' . HTML::attributes($attributes) . '></script>';
	}

	/**
	* Rewrites the filename
	* Gets the filemtime plus "?v=" for use with css and script files to help with getting browsers to grab a new version
	*
	* @param   string  $file  The path to the file inside the DOCROOT.
	* @return  string
	*/
	public static function add_cache_buster($file) {
		if (file_exists(DOCROOT . $file)) {
			$ext = pathinfo(DOCROOT . $file, PATHINFO_EXTENSION);
			return substr($file, 0, strlen($ext) * -1) . filemtime(DOCROOT . $file) . '.' . $ext;
		} else {
			return $file;
		}
	}
} // class XM_HTML