<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Error extends Controller_Public {
	protected $message;

	public $locale;

	public function before() {
		parent::before();

		// set the status to the action "name"
		$this->response->status((int) $this->request->action());

		$this->message = rawurldecode($this->request->param('message'));

		$this->locale = (empty($this->locale) ? $this->allowed_languages[0] : $this->locale);
	}

	/**
	 * Returns a 404 error status and 404 page.
	 *
	 * @return  void
	 */
	public function action_404() {
		if (empty($this->message)) {
			$this->message = 'The requested URL was not found.';
		}

		if (cl4::get_param('c_ajax', FALSE)) {
			echo AJAX_Status::ajax(array(
				'status' => AJAX_Status::NOT_FOUND_404,
				'debug_msg' => 'Requested URL: ' . $_SERVER['REQUEST_URI'],
			));
			exit;
		} else {
			$this->template->page_title = Response::$messages[404] . ' - ' . LONG_NAME;
			$this->template->body_html = View::factory('pages/' . $this->locale . '/error')
				->set('title', Response::$messages[404])
				->set('message', $this->message);
		}
	} // function action_404

	/**
	 * Returns a 500 error status and 500 page.
	 *
	 * @return  void
	 */
	public function action_500() {
		$this->template->page_title = Response::$messages[500] . ' - ' . LONG_NAME;
		$this->template->body_html = View::factory('pages/' . $this->locale . '/error')
			->set('title', Response::$messages[500])
			->set('message', $this->message);
	}

	/**
	 * Returns a 503 error status and 503 page.
	 *
	 * @return  void
	 */
	public function action_503() {
		$this->template->page_title = Response::$messages[503] . ' - ' . LONG_NAME;
		$this->template->body_html = View::factory('pages/' . $this->locale . '/error')
			->set('title', Response::$messages[503])
			->set('message', $this->message);
	}
}