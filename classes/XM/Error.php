<?php defined('SYSPATH') or die('No direct script access.');

class XM_Error {
	public static function error_log_dir() {
		$logs_dir = ABS_ROOT . DIRECTORY_SEPARATOR . 'logs';
		if ( ! is_dir($logs_dir) OR ! is_writable($logs_dir)) {
			throw new Kohana_Exception('Directory :dir must be writable',
				array(':dir' => Debug::path($logs_dir)));
		}

		$logs_dir = realpath($logs_dir) . DIRECTORY_SEPARATOR;
		$error_log_dir = $logs_dir . 'errors';

		if ( ! is_dir($error_log_dir)) {
			// Create the yearly directory
			mkdir($error_log_dir, 02777);

			// Set permissions (must be manually set to fix umask issues)
			chmod($error_log_dir, 02777);
		}

		return realpath($error_log_dir);
	}

	public static function error_log_file_prefix() {
		return Kohana::FILE_SECURITY . ' ?>'. PHP_EOL . PHP_EOL;
	}

	public static function error_log_file($file) {
		return realpath(Error::error_log_dir() . DIRECTORY_SEPARATOR . $file);
	}

	public static function delete_error_log_file($file) {
		$error_log_file = Error::error_log_file($file);
		if (empty($error_log_file)) {
			throw new Kohana_Exception('The error log file could not be found: :file', array(':file' => Debug::path($file)));
		}

		if ( ! unlink($error_log_file)) {
			throw new Kohana_Exception('The error log file could be deleted: :file', array(':file' => Debug::path($file)));
		}
	}

	public static function parse($file) {
		$error_log_file = Error::error_log_file($file);
		if (empty($error_log_file)) {
			throw new Kohana_Exception('The error log file could not be found: :file', array(':file' => Debug::path($file)));
		}

		$error_log = file_get_contents($error_log_file);
		if (empty($error_log)) {
			throw new Kohana_Exception('The error log file is empty: :file', array(':file' => Debug::path($file)));
		}

		$error_log = UTF8::substr($error_log, strlen(Error::error_log_file_prefix()));
		if (empty($error_log)) {
			throw new Kohana_Exception('The error data could not be found in :file', array(':file' => Debug::path($file)));
		}

		$error_data = json_decode($error_log, TRUE);
		Error::check_json_error();

		// attempt to find a similar error
		// based on the message, file and line number
		$similar_error = ORM::factory('Error_Log')
			->where('message', 'LIKE', $error_data['message'])
			->where('file', 'LIKE', $error_data['file'])
			->where('line', '=', $error_data['line'])
			->find();
		if ($similar_error->loaded()) {
			if (empty($similar_error->error_group_id)) {
				$error_group = ORM::factory('Error_Group')
					->save();
				$error_group_id = $error_group->pk();

				$similar_error->set('error_group_id', $error_group_id)
					->save();
			} else {
				$error_group_id = $similar_error->error_group_id;
			}
		} else {
			$error_group_id = 0;
		}

		$error_log_model = ORM::factory('Error_Log')
			->values(array(
				'error_group_id' => $error_group_id,
				'datetime' => date(Date::$timestamp_format, $error_data['timestamp']),
				'message' => $error_data['message'],
				'file' => $error_data['file'],
				'line' => $error_data['line'],
				'trace' => $error_data['trace'],
				'html' => $error_data['html'],
			));

		if (isset($error_data['server'])) {
			$error_log_model->values(array(
				'server' => $error_data['server'],
				'url' => $error_data['server']['PATH_INFO'], // change to something similar to Request::detect_uri()
				'remote_address' => $error_data['server']['REMOTE_ADDR'],
			));
		}

		foreach (array('get', 'post', 'files', 'cookie', 'session') as $_var) {
			if (isset($error_data[$_var])) {
				$error_log_model->set($_var, $error_data[$_var]);
			}
		}

		return $error_log_model->save();
	}

	public static function check_json_error() {
		switch (json_last_error()) {
			case JSON_ERROR_DEPTH:
				throw new Kohana_Exception('JSON Error: Maximum stack depth exceeded');
				break;
			case JSON_ERROR_STATE_MISMATCH:
				throw new Kohana_Exception('JSON Error: Underflow or the modes mismatch');
				break;
			case JSON_ERROR_CTRL_CHAR:
				throw new Kohana_Exception('JSON Error: Unexpected control character found');
				break;
			case JSON_ERROR_SYNTAX:
				throw new Kohana_Exception('JSON Error: Syntax error, malformed JSON');
				break;
			case JSON_ERROR_UTF8:
				throw new Kohana_Exception('JSON Error: Malformed UTF-8 characters, possibly incorrectly encoded');
				break;
		}
	}
}