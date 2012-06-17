<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'default' => array(
		// array of databases that all the changes need to be applied to
		'databases' => array(),
		// the table in which the changes that have been run should be stored
		// there should be a model for this table and it should extend change_script
		'log_table' => 'change_script',
		// the path to the scripts. ABS_ROOT will be added before this path
		'script_path' => DIRECTORY_SEPARATOR . 'change_scripts',
		// the change script file extensions that will be run
		'supported_exts' => array('SQL', 'SH', 'PHP'),
		// the path to use when executing sh change scripts
		'sh_path' => 'sh',
		// the path to use when executing PHP change scripts
		'php_path' => 'php',
	),
);