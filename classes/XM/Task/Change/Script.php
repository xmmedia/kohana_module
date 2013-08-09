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
		'database' => NULL,
		'databases' => NULL,
		'change_script' => NULL,
	);

	/**
	 * Config array. Stores the full config.
	 * @var  array
	 **/
	protected $_config = array();

	/**
	 * Stores the array of databases that were passed by the user.
	 * Used in conjunction with the array of databases in the config.
	 * @var  array
	 **/
	protected $_databases;

	/**
	 * The current database name.
	 * Set when looping through databases
	 * @var  string
	 **/
	protected $current_database;

	protected function _execute(array $params) {
		Minion_CLI::write('To use the change scripts, use the list, run and add tasks.');
	}

	protected function configure(array $params) {
		$default_config = (array) Kohana::$config->load('change_script.default');
		if (defined('CHANGE_SCRIPT_CONFIG') && CHANGE_SCRIPT_CONFIG != NULL && CHANGE_SCRIPT_CONFIG != '') {
			$custom_config = (array) Kohana::$config->load('change_script.' . CHANGE_SCRIPT_CONFIG);
			$this->_config = Arr::merge($default_config, $custom_config);
		} else {
			$this->_config = $default_config;
		}

		$_database = Arr::get($params, 'database');
		if ( ! empty($_database)) {
			$this->_databases = array(
				$_database
			);
		}
		$_databases = Arr::get($params, 'databases');
		if ( ! empty($_databases)) {
			$this->_databases = explode(',', $_databases);
		}

		if (empty($this->_config['databases'])) {
			Minion_CLI::write('!!! No databases are included in the config');
			exit(1);
		}

	}

	/**
	 * Runs SQL change scripts.
	 * Uses SQLParser to parse the SQL files and find the separate SQL commands.
	 * Each SQL statement will be run separately and the number of rows found or affected will be included in the returned string.
	 * The passed $file_path is not used within the method.
	 *
	 * @param  string  $file_path  The full path to the change script SQL file.
	 * @param  string  $file_contents  The change script file contents.
	 * @return  string
	 */
	protected function run_sql($file_path, $file_contents) {
		$log = '';

		// set the charset in the globals to the same as the database for use in the sql parser
		$GLOBALS['charset'] = Kohana::$config->load('database.' . (string) Database::instance() . '.charset');
		$GLOBALS['cfg']['Server']['extension'] = 'mysql';
		$GLOBALS['cfg']['MaxCharactersInDisplayedSQL'] = 1000;
		$GLOBALS['is_ajax_request'] = FALSE;

		if ( ! function_exists('PMA_SQP_parse')) {
			Kohana::load(Kohana::find_file('vendor', 'sqlparser/sqlparser.lib'));
		}

		$parsed_queries = PMA_SQP_parse($file_contents);

		if (empty($parsed_queries)) {
			throw new Kohana_Exception('No queries were found');
		}

		$queries = array();
		$i = 0;
		foreach ($parsed_queries as $key => $sql_part) {
			if ($key === 'raw' || $key === 'len' || $sql_part['type'] === 'white_newline' || $sql_part['type'] === 'comment_ansi') {
				continue;
			}

			if ( ! isset($queries[$i])) {
				switch(strtoupper($sql_part['data'])) {
					case 'SELECT' :
						$type = Database::SELECT;
						break;
					case 'DELETE' :
						$type = Database::DELETE;
						break;
					case 'INSERT' :
						$type = Database::INSERT;
						break;
					case 'UPDATE' :
					case 'ALTER' :
						$type = Database::UPDATE;
						break;
					default :
						$type = NULL;
						break;
				}

				$queries[$i] = array(
					'type' => $type,
					'query' => '',
				);
			}

			if ($sql_part['type'] == 'punct_queryend') {
				$queries[$i]['query'] .= ';';
				++ $i;
			} else {
				$queries[$i]['query'] .= ($queries[$i]['query'] != '' ? ' ' : '') . $sql_part['data'];
			}
		} // foreach

		$log .= 'Found ' . count($queries) . ' queries to run.' . EOL;

		foreach ($queries as $query) {
			$result = Database::instance()->query($query['type'], $query['query']);
			$successful = TRUE;

			$type = UTF8::strtoupper(UTF8::substr($query['query'], 0, UTF8::strpos($query['query'], ' ')));
			$log .= $type . ' -- ' . $query['query'];

			switch ($type) {
				case 'INSERT' :
					$log .= ' -- ' . $result[1] . ' row' . Text::s($result[1]) . ' affected';
					break;
				case 'UPDATE' :
				case 'DELETE' :
				case 'ALTER' :
					$log .= ' -- ' . $result . ' row' . Text::s($result) . ' affected';
					break;
				case 'SELECT' :
					$log .= ' -- ' . count($result) . ' row' . Text::s(count($result)) . ' found';
					break;
			}

			$log .= EOL;
		} // foreach

		return $log;
	} // function run_sql

	/**
	 * Runs shell/sh commands.
	 * Uses the config parameter `'sh_path'` and the full path to the change script to run the shell script.
	 * The parameter $file_contents is not used within the method.
	 *
	 * @param  string  $file_path  The full path to the change script sh file.
	 * @param  string  $file_contents  The change script file contents.
	 * @return  string
	 */
	protected function run_sh($file_path, $file_contents) {
		return shell_exec($this->_config['sh_path'] . ' ' . $file_path);
	}

	/**
	 * Runs PHP scripts.
	 * Uses the config parameter `'php_path'` and the full path to the change script to run the PHP file.
	 * The parameter $file_contents is not used within the method.
	 *
	 * @param  string  $file_path  The full path to the change script PHP file.
	 * @param  string  $file_contents  The change script file contents.
	 * @return  string
	 */
	protected function run_php($file_path, $file_contents) {
		return shell_exec($this->_config['php_path'] . ' ' . $file_path);
	}

	/**
	 * Creates an array of all the full paths to all the change scripts in the `change_script/` directory.
	 * Any change scripts with extensions not in the config key `'supported_exts'` are eliminated.
	 * The array is sorted by the file names.
	 * The keys and values in the array will be the full paths to the change scripts.
	 *
	 * @return  array
	 */
	protected function all_change_scripts() {
		$change_script_path = realpath(ABS_ROOT . $this->_config['script_path']);

		$change_scripts = $this->list_files_in_dir($change_script_path);
		if (empty($change_scripts)) {
			return array();
		}

		// flatten the array: removes the sub arrays from list_files_in_dir()
		$change_scripts = Arr::flatten($change_scripts);
		// remove the full path from array values as we only want to store the relative path the change scripts dir in the db
		$change_scripts = str_replace($change_script_path . DIRECTORY_SEPARATOR, '', $change_scripts);

		foreach ($change_scripts as $change_script_full_path => $change_script) {
			$script_type = strtoupper(pathinfo($change_script_full_path, PATHINFO_EXTENSION));
			if ( ! in_array($script_type, $this->_config['supported_exts'])) {
				Minion_CLI::write(PHP_EOL . '!!! The change script "' . $change_script . '" will be skipped because it\'s an unsupported extension. !!!' . PHP_EOL);
				unset($change_scripts[$change_script_full_path]);
			}
		}

		ksort($change_scripts);

		return $change_scripts;
	} // function all_change_scripts

	/**
	 * Checks to see if the change scripts are all readable and not empty.
	 * If any file is not reabable or empty, it will return FALSE and echo an error message.
	 *
	 * @param  array  $change_scripts  The array of paths to the change scripts.
	 * @return  boolean
	 */
	protected function change_script_readable($change_scripts) {
		$not_readable = $empty = FALSE;
		foreach ($change_scripts as $change_script_full_path => $change_script) {
			if ( ! is_readable($change_script_full_path)) {
				$not_readable = TRUE;
				Minion_CLI::write(PHP_EOL . 'The change script "' . $change_script . '" is not readable by PHP.');
				continue;
			}

			$file_contents = UTF8::trim(file_get_contents($change_script_full_path));
			if (empty($file_contents)) {
				$empty = TRUE;
				Minion_CLI::write(PHP_EOL . 'The change script "' . $change_script . '" is empty.');
				continue;
			}
		}

		if ($not_readable) {
			Minion_CLI::write(PHP_EOL . '!!! 1 or more of the change scripts are not reabled by PHP. Change the permissions on these before attempting to run the change scripts. !!!' . PHP_EOL);
			return FALSE;
		}
		if ($empty) {
			Minion_CLI::write(PHP_EOL . '!!! 1 or more of the change scripts are empty files. All change scripts need to have content (at a minimum, a description). !!!' . PHP_EOL);
			return FALSE;
		}

		return TRUE;
	} // function change_script_readable

	/**
	 * Loads the list of applied change scripts from the `change_script` table
	 * and then checks which of the array of change scripts need to still be applied.
	 * Returns an array of the change scripts that still need to be applied.
	 *
	 * @param  array  $change_scripts  The array of all the existing change scripts.
	 * @return  array
	 */
	protected function change_scripts_to_apply($change_scripts) {
		$applied_change_scripts = DB::select('filename')
			->from($this->_config['log_table'])
			->order_by('applied')
			->execute()
			->as_array(NULL, 'filename');

		$to_apply_change_scripts = $change_scripts;
		foreach ($applied_change_scripts as $applied_change_script) {
			$to_apply_key = array_search($applied_change_script, $to_apply_change_scripts);
			if ($to_apply_key !== FALSE) {
				unset($to_apply_change_scripts[$to_apply_key]);
			}
		}

		return $to_apply_change_scripts;
	} // function change_scripts_to_apply

	/**
	 * Creates an array of all the files in a directory and it's sub directories.
	 * Each sub directory will have a nested array.
	 * The key and the value of any file will be the full path to the file.
	 * The key to sub directory array will be the full path the sub directory.
	 *
	 * @param  string  $directory  The directory to start with.
	 * @return  array
	 */
	protected function list_files_in_dir($directory) {
		$directory .= DIRECTORY_SEPARATOR;

		$found = array();

		if (is_dir($directory)) {
			// Create a new directory iterator
			$dir = new DirectoryIterator($directory);

			foreach ($dir as $file) {
				// Get the file name
				$filename = $file->getFilename();

				if ($filename[0] === '.' || $filename[strlen($filename)-1] === '~') {
					// Skip all hidden files and UNIX backup files
					continue;
				}

				// Relative filename is the array key
				$key = $directory . $filename;

				if ($file->isDir()) {
					if ($sub_dir = $this->list_files_in_dir($key)) {
						if (isset($found[$key])) {
							// Append the sub-directory list
							$found[$key] += $sub_dir;
						} else {
							// Create a new sub-directory list
							$found[$key] = $sub_dir;
						}
					}
				} else {
					if ( ! isset($found[$key])) {
						// Add new files to the list
						$found[$key] = realpath($file->getPathName());
					}
				} // if
			} // foreach
		} // if

		return $found;
	} // function list_files_in_dir

	/**
	 * Pulls the description from the file.
	 * If no description is found, an empty string will be returned.
	 *
	 * @param  string  $file_contents  The full contents of the change script.
	 * @return  string
	 */
	protected function find_description($file_contents) {
		$description_start = UTF8::strpos($file_contents, '@description') + 12;
		if ($description_start !== FALSE) {
			$description_end = UTF8::strpos($file_contents, "\n", $description_start);
			return UTF8::trim(UTF8::substr($file_contents, $description_start, $description_end - $description_start));
		} else {
			return '';
		}
	} // function find_description
}