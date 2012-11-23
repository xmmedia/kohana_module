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
class XM_Task_Change_Script_List extends Task_Change_Script {
	/**
	 * Lists of scripts that will be run when action run is called.
	 * Gets a list of all the change scripts and then checks to make sure all of them are readable.
	 * The loops through the databases loading the entire `change_script` table and then checking it against the scripts in the folder.
	 * A list of the changes to will be applied to each database is echo'd.
	 * If there is a problem switching to a database (using USE), the database will be skipped, along with an error being displayed.
	 *
	 *     php index.php --uri="change_script/list"
	 *
	 * @return void
	 */
	protected function _execute(array $params) {
		$this->configure($params);

		Minion_CLI::write(PHP_EOL . 'Checking which change scripts need to be run...');

		$change_scripts = $this->all_change_scripts();
		if (empty($change_scripts)) {
			Minion_CLI::write('No change scripts were found so no change scripts need to be run.');
			return;
		}

		Minion_CLI::write('There are a total of ' . count($change_scripts) . ' change scripts.');

		if ( ! $this->change_script_readable($change_scripts)) {
			return;
		}

		foreach ($this->_config['databases'] as $database) {
			// skip any databases that aren't in the databases property/array if it's been set
			if ($this->_databases !== NULL && ! in_array($database, $this->_databases)) {
				Minion_CLI::write(PHP_EOL . '!!! Skipping ' . $databases . ' because it\'s not in the list of configured databases');
				continue;
			}

			try {
				DB::query(NULL, "USE " . Database::instance()->quote_identifier($database))->execute();
			} catch (Exception $e) {
				Minion_CLI::write(PHP_EOL . '!!! Failed to select the database ' . $database . ': ' . Kohana_Exception::text($e));
				continue;
			}

			try {
				$to_apply_change_scripts = $this->change_scripts_to_apply($change_scripts);

				Minion_CLI::write(PHP_EOL . '-------' . PHP_EOL . $database);
				if ( ! empty($to_apply_change_scripts)) {
					Minion_CLI::write('The following ' . count($to_apply_change_scripts) . ' change scripts need to be run on `' . $database . '`:' . PHP_EOL);
					foreach ($to_apply_change_scripts as $change_script_full_path => $to_apply_change_script) {
						$file_contents = file_get_contents($change_script_full_path);

						$manual_command_pos = UTF8::strpos($file_contents, '@manual');
						if ($manual_command_pos !== FALSE) {
							Minion_CLI::write('   ' . $to_apply_change_script . ' -- change script execution will stop at this change script because of @manual in the file (the script will not be run)');
							// end the loop for the change scripts on this and move to the next db
							continue 2;
						} else {
							Minion_CLI::write('   ' . $to_apply_change_script);
						}
					} // foreach
				} else {
					Minion_CLI::write('** No changes scripts need to be run. **');
				}
			} catch (Exception $e) {
				Minion_CLI::write(PHP_EOL . '!!! Failed to check which change scripts need to be run on `' . $database . '`: ' . Kohana_Exception::text($e));
				continue;
			}
		}

		Minion_CLI::write(PHP_EOL . 'To run the change scripts use the run task.' . PHP_EOL);
	}
}