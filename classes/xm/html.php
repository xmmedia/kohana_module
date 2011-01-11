<?php defined('SYSPATH') or die ('No direct script access.');

class XM_HTML extends cl4_HTML {
	/**
	 * Creates a style sheet link element.
	 * Same as Kohana_HTML::style() but supports using //example.com/path/to/file.css and doesn't add a type="text/css"
	 * Same as cl4_HTML::style() but adds a the filemtime as a query string to the file path
	 *
	 *     echo HTML::style('media/css/screen.css');
	 *
	 * @param   string  file name
	 * @param   array   default attributes
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function style($file, array $attributes = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE && strpos($file, '//') !== 0) {
			// Add the base URL
			$file = URL::base($index) . $file . HTML::get_filemtime_str($file);
		}

		// Set the stylesheet link
		$attributes['href'] = $file;

		// Set the stylesheet rel
		$attributes['rel'] = 'stylesheet';

		return '<link' . HTML::attributes($attributes) . '>';
	} // function

	/**
	 * Creates a script link.
	 * Same as Kohana_HTML::script() but supports using //example.com/path/to/file.js and doesn't add a type="text/javascript"
	 * Same as cl4_HTML::script() but adds a the filemtime as a query string to the file path
	 *
	 *     echo HTML::script('media/js/jquery.min.js');
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   boolean  include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public static function script($file, array $attributes = NULL, $index = FALSE) {
		if (strpos($file, '://') === FALSE && strpos($file, '//') !== 0) {
			// Add the base URL
			$file = URL::base($index) . $file . HTML::get_filemtime_str($file);
		}

		// Set the script link
		$attributes['src'] = $file;

		return '<script' . HTML::attributes($attributes) . '></script>';
	} // function

	/**
	* Gets the filemtime plus "?v=" for use with css and script files to help with getting browsers to grab a new version
	*
	* @param   mixed   $file  The file to mtime, DOCROOT is appended before
	* @return  string  ?v=[time]
	*/
	public static function get_filemtime_str($file) {
		if (file_exists(DOCROOT . $file)) {
			return '?v=' . filemtime(DOCROOT . $file);
		}
	} // function get_filemtime_str
} // class XM_HTML