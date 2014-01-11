<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Error_Admin extends Controller_Private {
	public $page = 'error_admin';

	protected $no_auto_render_actions = array('download_html');

	protected $show_resolved = FALSE;

	public function before() {
		parent::before();

		$this->page_title_append = 'Error Admin - ' . $this->page_title_append;

		if ($this->auto_render) {
			$this->add_style('error_admin', 'xm/css/error_admin.css')
				->add_script('error_admin', 'xm/js/error_admin.min.js');
		}

		$this->show_resolved = $this->request->query('show_resolved');
		if ($this->show_resolved !== NULL) {
			$this->show_resolved = (boolean) $this->show_resolved;
			Session::instance()->set_path('error_admin.show_resolved', $this->show_resolved);
		} else {
			$this->show_resolved = (boolean) Session::instance()->path('error_admin.show_resolved');
		}
	}

	public function action_index() {
		$this->template->page_title = $this->page_title_append;
		$this->template->body_html = View::factory('error_admin/index')
			->set('group_list', implode(PHP_EOL, $this->error_group_list()))
			->bind('show_resolved', $this->show_resolved)
			->set('right_col', '<p class="no_data no_errors">Select an error to continue.</p>');
	}

	public function action_view_group() {
		$error_group = $this->retrieve_error_group();
		$error_log = $this->retrieve_error_log();

		$resolve_url = $this->uri('resolve', $error_group, $error_log) . '?resolve=';
		if ( ! $error_log->resolved) {
			$resolve_link = HTML::anchor($resolve_url . '1', 'Unresolved', array('class' => 'unresolved', 'title' => 'Mark the entire group of errors as Resolved.'));
		} else {
			$resolve_link = HTML::anchor($resolve_url . '0', 'Resolved', array('class' => 'resolved', 'title' => 'Mark the entire group of errors as Unresolved.'));
		}

		if ( ! empty($error_log->html)) {
			$html_file_link = HTML::anchor($this->uri('download_html', $error_group, $error_log), 'Download', array('target' => '_blank'));
		}

		$server_items = array();
		foreach ((array) $error_log->server as $key => $value) {
			$server_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($server_items)) {
			$server_items = implode(PHP_EOL, $server_items);
		} else {
			$server_items = '<p class="no_errors">No Server data.</p>';
		}

		$post_items = array();
		foreach ((array) $error_log->post as $key => $value) {
			$post_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($post_items)) {
			$post_items = implode(PHP_EOL, $post_items);
		} else {
			$post_items = '<p class="no_errors">No Post data.</p>';
		}

		$get_items = array();
		foreach ((array) $error_log->get as $key => $value) {
			$get_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($get_items)) {
			$get_items = implode(PHP_EOL, $get_items);
		} else {
			$get_items = '<p class="no_errors">No Get data.</p>';
		}

		$file_items = array();
		foreach ((array) $error_log->files as $key => $value) {
			$file_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($file_items)) {
			$file_items = implode(PHP_EOL, $file_items);
		} else {
			$file_items = '<p class="no_errors">No File data.</p>';
		}

		$cookie_items = array();
		foreach ((array) $error_log->cookie as $key => $value) {
			$cookie_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($cookie_items)) {
			$cookie_items = implode(PHP_EOL, $cookie_items);
		} else {
			$cookie_items = '<p class="no_errors">No Cookie data.</p>';
		}

		$session_items = array();
		foreach ((array) $error_log->session as $key => $value) {
			$session_items[] = (string) View::factory('error_admin/view_item')
				->bind('key', $key)
				->set('value', $value)
				->set('pre', TRUE);
		}
		if ( ! empty($session_items)) {
			$session_items = implode(PHP_EOL, $session_items);
		} else {
			$session_items = '<p class="no_errors">No Session data.</p>';
		}

		$occurance_count = $error_group->error_log->where('resolved', '=', 0)->count_all();
		$all_occurrance_count = $error_group->error_log->count_all();

		$_similar_errors = $error_group->error_log
			->where('id', '!=', $error_log->pk())
			->find_all();
		$similar_errors = $resolved_similar_errors = array();
		foreach ($_similar_errors as $_error_log) {
			$message_a = HTML::anchor($this->uri('view_group', $error_group, $_error_log), HTML::chars($_error_log->message));

			$li = '<li>'
					. '<div class="date">' . HTML::chars($_error_log->datetime) . '</div>'
					. '<div class="message">' . $message_a . '</div>'
				. '</li>';

			if ($_error_log->resolved) {
				$resolved_similar_errors[] = $li;
			} else {
				$similar_errors[] = $li;
			}
		}
		if ( ! empty($similar_errors) || ! empty($resolved_similar_errors)) {
			$similar_errors = '<li class="header"><div class="date">When</div><div class="message">Message</div></li>'
				. implode(PHP_EOL, $similar_errors);

			if ( ! empty($resolved_similar_errors)) {
				$similar_errors .= '<li class="header"><h3>Resolved</h3></li>'
					. implode(PHP_EOL, $resolved_similar_errors);
			}
		} else {
			$similar_errors = '<p class="no_errors">No similar errors.</p>';
		}

		$error_id = uniqid('error');

		$trace_items = array();
		foreach ($error_log->trace as $i => $step) {
			$trace_item = '<li>
				<p>
					<span class="file">';
					if ($step['file']) {
						$source_id = $error_id . 'source' . $i;
						$trace_item .= '<a href="#' . $source_id . '" onclick="return koggle(\'' . $source_id . '\')">' . Debug::path($step['file']) . ' [ ' . $step['line'] . ' ]</a>';
					} else {
						$trace_item .= '{' . __('PHP internal call') . '}';
					}
					$trace_item .= '</span>
						&raquo;
						' . $step['function']  . '(';

					if ($step['args']) {
						$args_id = $error_id . 'args' . $i;
						$trace_item .= '<a href="#' . $args_id . '" onclick="return koggle(\'' . $args_id . '\')">' . __('arguments') . '</a>';
					}
					$trace_item .= ')'
				 . '</p>';

				if (isset($args_id)) {
					$trace_item .= '<div id="' . $args_id . '" class="collapsed">
					<table cellspacing="0">';
					foreach ($step['args'] as $name => $arg) {
						$trace_item .= '<tr>
							<td><code>' . $name . '</code></td>
							<td><pre>' . $arg . '</pre></td>
						</tr>';
					}
					$trace_item .= '</table>
					</div>';
				}
				if (isset($source_id)) {
					$trace_item .= '<pre id="' . $source_id . '" class="source collapsed"><code>' . $step['source'] . '</code></pre>';
				}
			$trace_item .= '</li>';
			unset($args_id, $source_id);

			$trace_items[] = $trace_item;
		} // foreach trace
		if ( ! empty($trace_items)) {
			$trace_items = implode(PHP_EOL, $trace_items);
		} else {
			$trace_items = '<p class="no_errors">No backtrace available.</p>';
		}

		$right_col = View::factory('error_admin/view_group')
			->bind('error_log', $error_log)
			->bind('resolve_link', $resolve_link)
			->bind('occurance_count', $occurance_count)
			->bind('all_occurrance_count', $all_occurrance_count)
			->bind('html_file_link', $html_file_link)
			->bind('trace_items', $trace_items)
			->bind('server_items', $server_items)
			->bind('post_items', $post_items)
			->bind('get_items', $get_items)
			->bind('file_items', $file_items)
			->bind('cookie_items', $cookie_items)
			->bind('session_items', $session_items)
			->bind('similar_errors', $similar_errors);

		$this->template->page_title = $this->page_title_append;
		$this->template->body_html = View::factory('error_admin/index')
			->set('group_list', implode(PHP_EOL, $this->error_group_list()))
			->bind('show_resolved', $this->show_resolved)
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

		$this->redirect($this->uri('view_group', $error_group, $error_log));
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
		$error_group = ORM::factory('Error_Group', (int) $this->request->param('error_group_id'));
		if ( ! $error_group->loaded()) {
			throw new Kohana_Exception('The error group could not be found or no error group ID passed');
		}

		return $error_group;
	}

	protected function retrieve_error_log() {
		$error_log = ORM::factory('Error_Log', (int) $this->request->param('error_log_id'));
		if ( ! $error_log->loaded()) {
			throw new Kohana_Exception('The error log could not be found or no error log ID passed');
		}

		return $error_log;
	}

	protected function error_group_list() {
		$_error_groups = ORM::factory('Error_Group')
			->find_all();
		$error_groups = array();
		foreach ($_error_groups as $error_group) {
			if ($this->show_resolved) {
				$error_log = $error_group->error_log->find();
			} else {
				$error_log = $error_group->error_log->where('resolved', '=', 0)->find();
			}

			if ($error_log->loaded()) {
				if ($this->show_resolved) {
					$occurances = $error_group->error_log->count_all();
				} else {
					$occurances = $error_group->error_log->where('resolved', '=', 0)->count_all();
				}

				$error_groups[strtotime($error_log->datetime) . '-' . $error_group->pk()] = array(
					'error_group' => $error_group,
					'error_log' => $error_log,
					'occurances' => $occurances,
				);
			}
		}

		ksort($error_groups);

		$list = array();
		foreach ($error_groups as $error_group) {
			$view_url = URL::site($this->uri('view_group', $error_group['error_group']->pk(), $error_group['error_log']->pk()));

			$list[] = (string) View::factory('error_admin/error_group_list_item')
				->bind('error_log', $error_group['error_log'])
				->bind('occurances', $error_group['occurances'])
				->bind('view_url', $view_url);
		}

		if (empty($list)) {
			$list[] = '<li class="no_errors">No errors to display.</li>';
		}

		return $list;
	}

	protected function uri($action, $error_group, $error_log) {
		if (is_object($error_group)) {
			$error_group = $error_group->pk();
		}

		if (is_object($error_log)) {
			$error_log = $error_log->pk();
		}

		return Route::get('error_admin')->uri(array(
			'action' => $action,
			'error_group_id' => $error_group,
			'error_log_id' => $error_log,
		));
	}
}