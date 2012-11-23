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
	protected function _execute(array $params) {
		$this->configure($params);

		echo PHP_EOL, 'Checking which change scripts need to be run...', PHP_EOL;

		$change_scripts = $this->all_change_scripts();
		if (empty($change_scripts)) {
			echo 'No change scripts were found so no change scripts need to be run.', PHP_EOL;
			return;
		}

		echo 'There are a total of ', count($change_scripts), ' change scripts.', PHP_EOL;

		if ( ! $this->change_script_readable($change_scripts)) {
			return;
		}

		foreach ($this->_config['databases'] as $database) {
			// skip any databases that aren't in the databases property/array if it's been set
			if ($this->_databases !== NULL && ! in_array($database, $this->_databases)) {
				echo PHP_EOL, '!!! Skipping ', $databases, ' because it\'s not in the list of configured databases', PHP_EOL;
				continue;
			}

			try {
				DB::query(NULL, "USE " . Database::instance()->quote_identifier($database))->execute();
			} catch (Exception $e) {
				echo PHP_EOL, '!!! Failed to select the database ', $database, ': ', Kohana_Exception::text($e), PHP_EOL;
				continue;
			}

			try {
				$to_apply_change_scripts = $this->change_scripts_to_apply($change_scripts);

				echo PHP_EOL, '-------', PHP_EOL, $database, PHP_EOL;
				if ( ! empty($to_apply_change_scripts)) {
					echo 'The following ', count($to_apply_change_scripts), ' change scripts need to be run on `', $database, '`:', PHP_EOL, PHP_EOL;
					foreach ($to_apply_change_scripts as $change_script_full_path => $to_apply_change_script) {
						$file_contents = file_get_contents($change_script_full_path);

						$manual_command_pos = UTF8::strpos($file_contents, '@manual');
						if ($manual_command_pos !== FALSE) {
							echo '   ', $to_apply_change_script, ' -- change script execution will stop at this change script because of @manual in the file (the script will not be run)', PHP_EOL;
							// end the loop for the change scripts on this and move to the next db
							continue 2;
						} else {
							echo '   ', $to_apply_change_script, PHP_EOL;
						}
					} // foreach
				} else {
					echo '** No changes scripts need to be run. **' . PHP_EOL;
				}
			} catch (Exception $e) {
				echo PHP_EOL, '!!! Failed to check which change scripts need to be run on `', $database, '`: ', Kohana_Exception::text($e), PHP_EOL;
				continue;
			}
		}

		echo PHP_EOL, 'To run the change scripts use the run task.', PHP_EOL, PHP_EOL;
	}
}