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
class XM_Task_Change_Script_Run extends Task_Change_Script {
	/**
	 * Runs the change scripts.
	 * Gets a list of all the change scripts and then checks to make sure all of them are readable.
	 * The loops through the databases, loading the entire change_script table and then checking it against the list of change scripts.
	 * Each script that hasn't been run us run using a method named "run_[script type]", ie, run_sql or run_sh.
	 * Once the script is a complete, a record will be added to the `change_script` table along with any output from the change script.
	 * If there is a problem switching to a database (using USE), the database will be skipped, along with an error being displayed.
	 * If a change script fails, change scripts will stop being run for the current database and it'll move to the next database.
	 *
	 *     php index.php --uri="change_script/run"
	 *
	 * @return void
	 */
	protected function _execute(array $params) {
		$this->configure($params);

		Minion_CLI::write(PHP_EOL . 'Running the change scripts...');

		$change_scripts = $this->all_change_scripts();
		if (empty($change_scripts)) {
			Minion_CLI::write('No change scripts were found so no change scripts will be run.');
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

			$this->current_database = $database;

			try {
				DB::query(NULL, "USE " . Database::instance()->quote_identifier($database))->execute();
			} catch (Exception $e) {
				Minion_CLI::write(PHP_EOL . '!!! Failed to select the database ' . $database . ': ' . Kohana_Exception::text($e));
				continue;
			}

			try {
				$to_apply_change_scripts = $this->change_scripts_to_apply($change_scripts);

				Minion_CLI::write(PHP_EOL . '-------' . PHP_EOL . $database);
				if (empty($to_apply_change_scripts)) {
					Minion_CLI::write('** No changes scripts need to be run. **');
					continue;
				}

				Minion_CLI::write('Running change scripts...' . PHP_EOL);
				foreach ($to_apply_change_scripts as $change_script_full_path => $to_apply_change_script) {
					fwrite(STDOUT, '   ' . $to_apply_change_script);

					$file_contents = file_get_contents($change_script_full_path);

					// if @manual is found in the file, the change script needs to be run manually
					// so stop execution on this database and move to the next
					$manual_command_pos = UTF8::strpos($file_contents, '@manual');
					if ($manual_command_pos !== FALSE) {
						Minion_CLI::write(' -- !!! execution stopped !!!' . PHP_EOL . '    -- The change script ' . $to_apply_change_script . ' has the @manual command. Run this script manually and use the action "change_script/add" to add the script to change_script table.' . PHP_EOL);
						continue 2;
					}

					// look for the description the file
					$description = $this->find_description($file_contents);

					$script_type = strtoupper(pathinfo($change_script_full_path, PATHINFO_EXTENSION));

					// run the script using the method for it's script type
					$method = 'run_' . strtolower($script_type);
					$log = $this->$method($change_script_full_path, $file_contents);

					// log that the change log has been run
					DB::insert($this->_config['log_table'], array('filename', 'type', 'applied', 'description', 'log'))
						->values(array($to_apply_change_script, $script_type, DB::expr("NOW()"), $description, $log))
						->execute();
					fwrite(STDOUT, ' -- done' . PHP_EOL);
				}

			} catch (Exception $e) {
				if (isset($to_apply_change_script)) {
					$msg = 'There was an error while running the change script "' . $to_apply_change_script . '" on `' . $database . '`';
				} else {
					$msg = 'There was an error while running the change scripts on `' . $database . '`';
				}
				Minion_CLI::write(PHP_EOL . '!!! There was an error while running the change script "' . $to_apply_change_script . '" on `' . $database . '`: ' . Kohana_Exception::text($e));
				continue;
			}
		}

		$this->current_database = NULL;

		Minion_CLI::write(PHP_EOL . 'The change scripts that needed to be run have been run successfully.' . PHP_EOL);
	}
}