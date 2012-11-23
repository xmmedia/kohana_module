<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Executes/runs change scripts.
 *
 * This will run any change scripts that haven't already been run on the database based on the list in `change_scripts` directory.
 * Changes scripts are located in `change_scripts/` or the path configured in `'script_path'`.
 * It will check and possibly run all the files in the root directory and any sub directories.
 * The databases that the change scripts will run on is based on the array of databases in the config option `'databases'`.
 *
 * To find out which scripts will be run, use: `php index.php --uri="change_script/list"`
 *
 * To run the change scripts, use: `php index.php --uri="change_script/run"`
 *
 * To list or run the change scripts on specific databases, add `--database=db_name` or `--datbases="db_name1,db_name2,..."`
 * to the above commands, such as: `php index.php --uri="change_script/list" --database="db_name"`.
 * Databases must also be in the config.
 *
 * To add a change script to the `change_script` table manually, use the add action: `php index.php --uri="change_script/add" --change_script="path/filename"`
 *
 * To force a script to be run manually, add "@manual" anywhere in the file. Execution will stop when it gets to this file.
 *
 * By default, the route for this controller is disabled. To enable, set the config xm.routes.change_script to TRUE
 * or add the route found in init.php to your bootstrap.
 *
 *    php index.php --task=change:script:list
 *    php index.php --task=change:script:run
 *    php index.php --task=change:script:add --change_script="<path/to/file>" (within change_scripts dir)
 *    --database=<db_name>
 *    --databases=<db_name1>,<db_name2>,...
 *
 * @package    XM
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class XM_Task_Change_Script extends Minion_Task {
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