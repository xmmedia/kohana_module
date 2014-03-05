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
class XM_Task_Change_Script_Add extends Task_Change_Script {
	/**
	 * Adds the change script to the `change_script` table without actually running the change script.
	 * Useful for @manual scripts.
	 * "change_script" is the file path relative to the change_script directory.
	 * Does **NOT** check to see if the script has already been run.
	 * "@manual" will be added as the log for the change script.
	 * If there is a problem switching to a database (using USE), the database will be skipped, along with an error being displayed.
	 *
	 *     php index.php --uri="change_script/add" --change_script="path/filename"
	 *
	 * @return void
	 */
	protected function _execute(array $params) {
		$this->configure($params);

		Minion_CLI::write(PHP_EOL . 'Logging the change script...');

		$change_script = Arr::get($params, 'change_script');

		$change_script_full_path = realpath(ABS_ROOT . $this->_config['script_path'] . DIRECTORY_SEPARATOR. $change_script);
		if ( ! is_readable($change_script_full_path)) {
			Minion_CLI::write(PHP_EOL . '!!! The change script is not reabled by PHP. Change the permissions on these before attempting to run the change scripts. !!!' . PHP_EOL);
			return;
		}

		$file_contents = file_get_contents($change_script_full_path);
		if (empty($file_contents)) {
			Minion_CLI::write(PHP_EOL . '!!! The change script is an empty file. All change scripts need to have content (at a minimum, a description). !!!' . PHP_EOL);
			return;
		}

		// look for the description the file
		$description = $this->find_description($file_contents);

		$script_type = strtoupper(pathinfo($change_script_full_path, PATHINFO_EXTENSION));

		foreach ($this->_config['databases'] as $database) {
			// skip any databases that aren't in the databases property/array if it's been set
			if ($this->_databases !== NULL && ! in_array($database, $this->_databases)) {
				Minion_CLI::write(PHP_EOL . '!!! Skipping ', $databases, ' because it\'s not in the list of configured databases');
				continue;
			}

			fwrite(STDOUT, PHP_EOL . '-------' . PHP_EOL . $database);

			$this->current_database = $database;

			try {
				DB::query(NULL, "USE " . Database::instance()->quote_identifier($database))->execute();
			} catch (Exception $e) {
				Minion_CLI::write(PHP_EOL . '!!! Failed to select the database ' . $database . ': ' . Kohana_Exception::text($e));
				continue;
			}

			DB::insert($this->_config['log_table'], array('filename', 'type', 'applied', 'description', 'log'))
				->values(array($change_script, $script_type, Date::formatted_time(), $description, '@manual'))
				->execute();
			fwrite(STDOUT, ' -- done');
		} // foreach

		$this->current_database = NULL;

		Minion_CLI::write(PHP_EOL . 'The change script was logged successfully.' . PHP_EOL);
	}

	public function build_validation(Validation $validation) {
		return parent::build_validation($validation)
			->rule('change_script', 'not_empty');
	}
}