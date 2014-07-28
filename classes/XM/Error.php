<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Methods for working with errors for the Error Admin.
 *
 * @package    XM
 * @category   Errors
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
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

	public static function error_user_filename($pk) {
		return XMFile::clean_filename('error_details-' . strtolower(LONG_NAME) . '-' . $pk . '.html');
	}

	public static function delete_error_log_file($files) {
		$files = (array) $files;

		foreach ($files as $file) {
			$error_log_file = Error::error_log_file($file);
			if (empty($error_log_file)) {
				throw new Kohana_Exception('The error log file could not be found: :file', array(':file' => Debug::path($file)));
			}

			if ( ! unlink($error_log_file)) {
				throw new Kohana_Exception('The error log file could be deleted: :file', array(':file' => Debug::path($file)));
			}
		}
	}

	public static function parse($files) {
		$files = (array) $files;

		$error_log_ids = array();

		foreach ($files as $file) {
			$error_log_file = Error::error_log_file($file);
			if (empty($error_log_file)) {
				Kohana::$log->add(Log::DEBUG, 'The error log file could not be found: :file', array(':file' => Debug::path($file)));
				continue;
			}

			$error_log = file_get_contents($error_log_file);
			if (empty($error_log)) {
				Kohana::$log->add(Log::DEBUG, 'The error log file is empty: :file', array(':file' => Debug::path($file)));
				continue;
			}

			$error_log = UTF8::substr($error_log, strlen(Error::error_log_file_prefix()));
			if (empty($error_log)) {
				Kohana::$log->add(Log::DEBUG, 'The error data could not be found in :file', array(':file' => Debug::path($file)));
				continue;
			}

			$error_data = json_decode($error_log, TRUE);
			Error::check_json_error();

			$error_log_model = ORM::factory('Error_Log')
				->values(array(
					'datetime' => date(Date::$timestamp_format, $error_data['timestamp']),
					'message' => $error_data['message'],
					'file' => $error_data['file'],
					'line' => $error_data['line'],
					'code' => $error_data['code'],
					'trace' => $error_data['trace'],
					'html' => $error_data['html'],
				));

			$message_parts = Error::message_parts($error_log_model->message);

			// attempt to find a similar error
			// based on the message, file and line number
			$similar_error_groups = ORM::factory('Error_Group')
				->where('file', 'LIKE', $error_data['file'])
				->where('line', '=', $error_data['line'])
				->find_all();
			// loop through the errors to see if any of the messages are similar
			if ($similar_error_groups->count() > 0) {
				foreach ($similar_error_groups as $similar_error_group) {
					if ($message_parts === $similar_error_group->data['message_parts']) {
						$error_log_model->error_group_id = $similar_error_group->pk();
					}
				}
			}

			// if the the error group is still not set, create a new group
			if (empty($error_log_model->error_group_id)) {
				$error_group = Error::new_error_group($error_log_model->file, $error_log_model->line, $message_parts);
				$error_log_model->error_group_id = $error_group->pk();
			}

			if (isset($error_data['server'])) {
				$error_log_model->values(array(
					'server' => $error_data['server'],
					'url' => Error::detect_uri($error_data['server']),
					'remote_address' => (isset($error_data['server']['REMOTE_ADDR']) ? $error_data['server']['REMOTE_ADDR'] : NULL),
				));
			}

			foreach (array('get', 'post', 'files', 'cookie', 'session') as $_var) {
				if (isset($error_data[$_var])) {
					$error_log_model->set($_var, $error_data[$_var]);
				}
			}

			$error_log_model->save();

			$error_log_ids[$error_log_model->error_group_id][] = $error_log_model->pk();
		}

		$reoccurance_email_time = (int) Kohana::$config->load('error_admin.reoccurance_email_time');

		foreach ($error_log_ids as $error_group_id => $_error_log_ids) {
			$error_group = ORM::factory('Error_Group', $error_group_id);
			if ( ! $error_group->loaded()) {
				throw new Kohana_Exception('Unable to load the error group: :group_id', array(':group_id' => $error_group_id));
			}

			// find the unresolved last error log before the one(s) we've just added
			$last_error_log = $error_group->error_log
				->where('resolved', '=', 0)
				->where('id', 'NOT IN', $_error_log_ids)
				->find();

			$send_email = FALSE;
			if ( ! $last_error_log->loaded()) {
				$send_email = TRUE;
			} else if (strtotime($last_error_log->datetime) < strtotime('-' . $reoccurance_email_time . ' minutes')) {
				$send_email = TRUE;
			}

			// can't send an email if there's no error email
			$error_email_address = XM::get_error_email();
			if (empty($error_email_address)) {
				$send_email = FALSE;
			}

			if ($send_email) {
				$error_log_model = ORM::factory('Error_Log', $_error_log_ids[0]);
				if ( ! $error_log_model->loaded()) {
					throw new Kohana_Exception('Unable to load the error log: :error_log_id', array(':error_log_id' => $_error_log_ids[0]));
				}

				$occurances = $error_group->error_log
					->where('resolved', '=', 0)
					->count_all();

				$view_error_url = URL::site(Route::get('error_admin')->uri(array(
					'action' => 'view_group',
					'error_group_id' => $error_group->pk(),
					'error_log_id' => $error_log_model->pk(),
				)));

				// will be limited to 100 chars
				$additional_subject = ' in '
					. substr($error_log_model->file, strripos($error_log_model->file, DIRECTORY_SEPARATOR) + 1)
					. ':' . $error_log_model->line
					. ' - ' . $error_log_model->message;

				$trace_file_list = array();
				foreach ($error_log_model->trace as $_trace) {
					if (isset($_trace['file']) && isset($_trace['line'])) {
						$trace_file_list[] = Debug::path($_trace['file']) . ':' . $_trace['line'];
					}
				}

				$error_email = new Mail();
				$error_email->AddAddress($error_email_address);
				$error_email->Subject = 'Error on ' . LONG_NAME . Text::limit_chars($additional_subject);
				$error_email->MsgHTML((string) View::factory('error_admin/notification_email')
					->bind('error_log_model', $error_log_model)
					->bind('trace_file_list', $trace_file_list)
					->bind('occurances', $occurances)
					->bind('view_error_url', $view_error_url));

				$error_email->AddStringAttachment($error_log_model->html, Error::error_user_filename($error_log_model->pk()), 'base64', 'text/html');

				$error_email->Send();
			}
		}
	}

	public static function new_error_group($file, $line, $message_parts) {
		return ORM::factory('Error_Group')
			->values(array(
				'file' => $file,
				'line' => $line,
				'data' => array(
					'message_parts' => $message_parts,
				),
			))
			->save();
	}

	public static function message_parts($message) {
		$_message_parts = explode(' ', $message);

		$message_parts = array();
		foreach ($_message_parts as $_message_part) {
			$_message_part = trim($_message_part);
			if (strlen($_message_part) > 3) {
				$message_parts[] = $_message_part;
			}
		}

		return $message_parts;
	}

	/**
	 * Automatically detects the URI of the main request using PATH_INFO,
	 * REQUEST_URI, PHP_SELF or REDIRECT_URL.
	 *
	 *     $uri = Request::detect_uri();
	 *
	 * @return  string  URI of the main request
	 * @throws  Kohana_Exception
	 * @since   3.0.8
	 */
	public static function detect_uri($server) {
		if ( ! empty($server['PATH_INFO'])) {
			// PATH_INFO does not contain the docroot or index
			$uri = $server['PATH_INFO'];
		} else {
			// REQUEST_URI and PHP_SELF include the docroot and index

			if (isset($server['REQUEST_URI'])) {
				/**
				 * We use REQUEST_URI as the fallback value. The reason
				 * for this is we might have a malformed URL such as:
				 *
				 *  http://localhost/http://example.com/judge.php
				 *
				 * which parse_url can't handle. So rather than leave empty
				 * handed, we'll use this.
				 */
				$uri = $server['REQUEST_URI'];

				if ($request_uri = parse_url($server['REQUEST_URI'], PHP_URL_PATH)) {
					// Valid URL path found, set it.
					$uri = $request_uri;
				}

				// Decode the request URI
				$uri = rawurldecode($uri);
			} else if (isset($server['PHP_SELF'])) {
				$uri = $server['PHP_SELF'];
			} else if (isset($server['REDIRECT_URL'])) {
				$uri = $server['REDIRECT_URL'];
			} else {
				return NULL;
			}

			// Get the path from the base URL, including the index file
			$base_url = parse_url(Kohana::$base_url, PHP_URL_PATH);

			if (strpos($uri, $base_url) === 0) {
				// Remove the base URL from the URI
				$uri = (string) substr($uri, strlen($base_url));
			}

			if (Kohana::$index_file && strpos($uri, Kohana::$index_file) === 0) {
				// Remove the index file from the URI
				$uri = (string) substr($uri, strlen(Kohana::$index_file));
			}
		}

		return $uri;
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