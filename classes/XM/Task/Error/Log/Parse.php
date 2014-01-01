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
		'file' => NULL,
	);

	/**
	 * Error log parse.
	 *
	 * @return null
	 */
	protected function _execute(array $params) {
		Error::parse($params['file']);
		echo 'Parsed error log: ' . $params['file'] . PHP_EOL;
	}

	public function build_validation(Validation $validation) {
		return parent::build_validation($validation)
			->rule('file', 'not_empty');
	}
}