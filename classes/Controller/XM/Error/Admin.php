<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Error_Admin extends Controller_Private {
	public $page = 'error_admin';

	protected $no_auto_render_actions = array('download_html');

	public function before() {
		parent::before();

		$this->page_title_append = 'Error Admin - ' . $this->page_title_append;

		if ($this->auto_render) {
			$this->add_style('error_admin', 'xm/css/error_admin.css')
				->add_script('error_admin', 'xm/js/error_admin.min.js');
		}
	}

	public function action_index() {
		$this->template->page_title = $this->page_title_append;
		$this->template->body_html = View::factory('error_admin/index')
			->set('group_list', implode(PHP_EOL, $this->error_group_list()))
			->set('right_col', '<p class="no_errors">Select an error to continue.</p>');
	}

	public function action_view_group() {
		$error_group = $this->retrieve_error_group();
		$error_log = $this->retrieve_error_log();

		$resolve_url = Route::get('error_admin')->uri(array('action' => 'resolve', 'id' => $error_group->pk())) . '?error_log_id=' . $error_log->pk() . '&resolve=';
		if ( ! $error_log->resolved) {
			$resolve_link = HTML::anchor($resolve_url . '1', 'Unresolved', array('class' => 'unresolved', 'title' => 'Mark the entire group of errors as Resolved.'));
		} else {
			$resolve_link = HTML::anchor($resolve_url . '0', 'Resolved', array('class' => 'resolved', 'title' => 'Mark the entire group of errors as Unresolved.'));
		}

		$occurance_count = $error_group->error_log
			->where('resolved', '=', 0)
			->count_all();

		if ( ! empty($error_log->html)) {
			$html_file_link = HTML::anchor(Route::get('error_admin')->uri(array('action' => 'download_html', 'id' => $error_group->pk())) . '?error_log_id=' . $error_log->pk(), 'Download', array('target' => '_blank'));
		}

		$server_items = array();
		foreach ((array) $error_log->server as $key => $value) {
			$server_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', Debug::vars($value))
				->set('pre', TRUE);
		}

		$post_items = array();
		foreach ((array) $error_log->post as $key => $value) {
			$post_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', Debug::vars($value))
				->set('pre', TRUE);
		}

		$get_items = array();
		foreach ((array) $error_log->get as $key => $value) {
			$get_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', Debug::vars($value))
				->set('pre', TRUE);
		}

		$cookie_items = array();
		foreach ((array) $error_log->cookie as $key => $value) {
			$cookie_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', Debug::vars($value))
				->set('pre', TRUE);
		}

		$session_items = array();
		foreach ((array) $error_log->session as $key => $value) {
			$session_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', Debug::vars($value))
				->set('pre', TRUE);
		}

		$right_col = View::factory('error_admin/view_group')
			->bind('error_log', $error_log)
			->bind('resolve_link', $resolve_link)
			->bind('occurance_count', $occurance_count)
			->bind('html_file_link', $html_file_link)
			->set('server_items', implode(PHP_EOL, $server_items))
			->set('post_items', implode(PHP_EOL, $post_items))
			->set('get_items', implode(PHP_EOL, $get_items))
			->set('cookie_items', implode(PHP_EOL, $cookie_items))
			->set('session_items', implode(PHP_EOL, $session_items));

		$this->template->page_title = $this->page_title_append;
		$this->template->body_html = View::factory('error_admin/index')
			->set('group_list', implode(PHP_EOL, $this->error_group_list()))
			->bind('right_col', $right_col);
	}

	public function action_resolve() {
		$error_group = $this->retrieve_error_group();
		$error_log = $this->retrieve_error_log();

		$set_resolve_to = (bool) $this->request->query('resolve');

		$error_logs = $error_group->error_log
			->where('resolved', '=', ($set_resolve_to ? 0 : 1))
			->find_all();
		foreach ($error_logs as $_error_log) {
			$_error_log->set('resolved', ($set_resolve_to ? 1 : 0))
				->save();
		}

		$count = $error_logs->count();
		Message::add($count . ' ' . Inflector::plural('error', $count) . ' ' . Text::have($count) . ' been marked as resolved.', Message::$notice);

		$this->redirect(Route::get('error_admin')->uri(array('action' => 'view_group', 'id' => $error_group->pk())) . '?error_log_id=' . $error_log->pk());
	}

	public function action_download_html() {
		$error_group = $this->retrieve_error_group();
		$error_log = $this->retrieve_error_log();

		if (empty($error_log->html)) {
			throw new Kohana_Exception('The HTML file/field is empty');
		}

		$this->response->body($error_log->html);
		$this->response->send_file(TRUE, 'error_details.html');
	}

	protected function retrieve_error_group() {
		$error_group = ORM::factory('Error_Group', (int) $this->request->param('id'));
		if ( ! $error_group->loaded()) {
			throw new Kohana_Exception('The error group could not be found or no error group ID passed');
		}

		return $error_group;
	}

	protected function retrieve_error_log() {
		$error_log = ORM::factory('Error_Log', (int) $this->request->query('error_log_id'));
		if ( ! $error_log->loaded()) {
			throw new Kohana_Exception('The error log could not be found or no error log ID passed');
		}

		return $error_log;
	}

	protected function error_group_list() {
		$error_groups = DB::select(array('eg.id', 'error_group_id'), array('el.id', 'error_log_id'))
			->select('el.message', 'el.datetime', array(DB::expr('COUNT(el.id)'), 'occurances'))
			->from(array('error_group', 'eg'))
			->join(array('error_log', 'el'), 'INNER')
				->on('el.error_group_id', '=', 'eg.id')
			->where('el.resolved', '=', 0)
			->group_By('eg.id')
			->order_by('el.datetime', 'DESC')
			->execute();

		$list = array();
		foreach ($error_groups as $error_group) {
			$view_url = URL::site(Route::get('error_admin')->uri(array(
					'action' => 'view_group',
					'id' => $error_group['error_group_id'],
				)) . '?error_log_id=' . $error_group['error_log_id']);

			$list[] = (string) View::factory('error_admin/error_group_list_item')
				->bind('error_group', $error_group)
				->bind('view_url', $view_url);
		}

		if (empty($list)) {
			$list[] = '<li class="no_errors">No errors to display.</li>';
		}

		return $list;
	}
}