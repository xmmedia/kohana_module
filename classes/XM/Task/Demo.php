<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This is a Demo task.
 *
 * @package    Kohana
 * @category   Blah
 * @author     Kohana Team
 * @copyright  (c) 2009-2011 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class XM_Task_Demo extends Minion_Task {
	protected $_options = array(
		'foo' => 'bar',
		'bar' => NULL,
	);

	/**
	 * This is a demo task
	 *
	 * @return null
	 */
	protected function _execute(array $params) {
		var_dump($params);
		echo 'foobar';
	}

	public function build_validation(Validation $validation) {
		return parent::build_validation($validation)
			->rule('foo', 'not_empty') // Require this param
			->rule('bar', 'not_empty') // This param should not be empty
			->rule('bar', 'numeric'); // This param should be numeric
	}
}