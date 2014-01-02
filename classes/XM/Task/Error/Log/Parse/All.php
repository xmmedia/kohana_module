<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Error log parse.
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class XM_Task_Error_Log_Parse extends Minion_Task {
	protected $_options = array(
		'delete_file' => TRUE,
	);

	/**
	 * Error log parse.
	 *
	 * @return null
	 */
	protected function _execute(array $params) {
		Error::parse($params['file']);

		if ($params['delete_file']) {
			Error::delete_error_log_file($params['file']);
		}

		Minion_CLI::write('Parsing successful');
		Minion_CLI::write('Parsed error log: ' . $params['file']);
	}

	/*public function build_validation(Validation $validation) {
		return parent::build_validation($validation)
			->rule('file', 'not_empty');
	}*/
}