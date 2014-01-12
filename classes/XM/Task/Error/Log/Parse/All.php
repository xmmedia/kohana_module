<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Parses all the existing error logs for the error log admin.
 * Will look for error logs in the directory returned by `Error::error_log_dir()`
 * typically `ABS_ROOT/logs/errors/`.
 * The default is to delete the files once they've been parsed.
 * To skip deleting the files, set the `delete_files` parameter to `0`.
 *
 * Examples
 *
 *     ./minion error:log:parse:all
 *     ./minion error:log:parse:all --delete_files=0
 *     // or
 *     php index.php --task=error:log:parse:all
 *     php index.php --task=error:log:parse:all --delete_files=0
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
class XM_Task_Error_Log_Parse_All extends Minion_Task {
	protected $_options = array(
		'delete_files' => TRUE,
	);

	protected function _execute(array $params) {
		$_error_log_files = Directory_Helper::list_files(Error::error_log_dir());
		$error_log_files = array();
		foreach ($_error_log_files as $_error_log_file) {
			$error_log_files[] = str_replace(Error::error_log_dir() . DIRECTORY_SEPARATOR, '', $_error_log_file);
		}

		$file_count = count($error_log_files);

		Error::parse($error_log_files);

		if ($params['delete_files']) {
			Error::delete_error_log_file($error_log_files);
		}

		foreach ($error_log_files as $error_log_file) {
			Minion_CLI::write('Parsed error log: ' . $error_log_file);
		}

		Minion_CLI::write('Parsing successful. Parsed ' . $file_count . ' ' . Inflector::plural('file', $file_count) . '.');
	}
}