<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Parses an error log for the error log admin.
 * The error log file needs to be located in the directory returned by `Error::error_log_dir()`
 * typically `ABS_ROOT/logs/errors/`.
 * The file is required and is passed in with the `file` parameter.
 * The default is to delete the file once it's been parsed.
 * To skip deleting the file, set the `delete_file` parameter to `0`.
 *
 * Examples
 *
 *     ./minion error:log:parse --file=1389484642_52d1dasadfbe4.php
 *     // or
 *     php index.php --task=error:log:parse --file=1389484642_52d1dasadfbe4.php
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
class XM_Task_Error_Log_Parse extends Minion_Task {
	protected $_options = array(
		'file' => NULL,
		'delete_file' => TRUE,
	);

	protected function _execute(array $params) {
		Error::parse($params['file']);

		if ($params['delete_file']) {
			Error::delete_error_log_file($params['file']);
		}

		Minion_CLI::write('Parsing successful');
		Minion_CLI::write('Parsed error log: ' . $params['file']);
	}

	public function build_validation(Validation $validation) {
		return parent::build_validation($validation)
			->rule('file', 'not_empty');
	}
}