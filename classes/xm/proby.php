<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Proby notifier class.
 * Used with cron to track the running of a background script and aleat on errors.
 * Documentation on Proby can be found here: http://probyapp.com/documentation/
 *
 * @package    XM
 * @category   Exception
 * @author     XM Media Inc.
 * @copyright  (c) 2011 XM Media Inc.
 */
class XM_Proby {
	/**
	 * @var  string  The API key for your Proby account.
	 */
	public static $api_key;

	/**
	 * @var  string  The HTTPS address to send the notifications to.
	 */
	public static $end_point = 'https://proby.signalhq.com/tasks/';

	/**
	 * @var  string  Stores the instances. There will be 1 instance for each task ID.
	 */
	protected static $_instance;

	/**
	 * Singleton pattern
	 *
	 * @param  string  $task_id  The task ID to get the instance for.
	 * @return  Proby
	 */
	public static function instance($task_id) {
		if ( ! isset(Proby::$_instance['$task_id'])) {
			// Create a new session instance
			Proby::$_instance[$task_id] = new Proby($task_id);
		}

		return Proby::$_instance[$task_id];
	}

	/**
	 * @var  string  The current task ID.
	 */
	protected $task_id;

	/**
	 * Sets the task ID in the object.
	 *
	 * @param  string  $task_id  The task ID for the object.
	 */
	public function __construct($task_id) {
		$this->task_id = $task_id;
	}

	/**
	 * Sends the start notification to Proby.
	 *
	 * @return  Proby
	 */
	public function start() {
		return $this->notify('start');
	}

	/**
	 * Sends the finish notification to Proby.
	 *
	 * @return  Proby
	 */
	public function finish() {
		return $this->notify('finish');
	}

	/**
	 * Sends an finish error notification to Proby along with the error message.
	 *
	 * @param  string  $error_message  The error message. Max 1000 characters.
	 * @return  Proby
	 */
	public function error($error_message) {
		return $this->notify('finish', $error_message);
	}

	/**
	 * Performs the POST to Proby.
	 *
	 * @param  string  $status  The status: start or finish
	 * @param  string  $error_message  The error message to send to Proby.
	 * @return  Proby
	 */
	public function notify($status, $error_message = NULL) {
		$request = Request::factory(self::$end_point . $this->task_id . '/' . $status)
			->method('POST')
			->headers('api_key', self::$api_key);

		// don't verify the ssl cert (we don't have the proper CA)
		$request->client()
			->options(CURLOPT_SSL_VERIFYPEER, 0)
			->options(CURLOPT_SSL_VERIFYHOST, 0);

		if ($error_message !== NULL) {
			$request->post('failed', "true")
				->post('error_message', Text::limit_chars($error_message, 1000));
		}

		$request->execute();

		return $this;
	} // function notify
} // class