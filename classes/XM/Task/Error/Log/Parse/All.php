<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Error log parse all.
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class XM_Task_Error_Log_Parse_All extends Minion_Task {
	protected $_options = array(
		'delete_files' => TRUE,
	);

	/**
	 * Error log parse all.
	 *
	 * @return null
	 */
	protected function _execute(array $params) {
		$file_count = 0;

		$error_log_files = Directory_Helper::list_files(Error::error_log_dir(), FALSE);
		foreach ($error_log_files as $error_log_file) {
			$error_log_file = str_replace(Error::error_log_dir() . DIRECTORY_SEPARATOR, '', $error_log_file);

			Error::parse($error_log_file);

			if ($params['delete_files']) {
				Error::delete_error_log_file($error_log_file);
			}

			Minion_CLI::write('Parsed error log: ' . $error_log_file);

			++ $file_count;
		}

		Minion_CLI::write('Parsing successful. Parsed ' . $file_count . ' ' . Inflector::plural('file', $file_count) . '.');
	}
}