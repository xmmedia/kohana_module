<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_UserAdmin extends Controller_Base {
	public $auth_required = TRUE;
	public $secure_actions = array(
		'index' => 'useradmin/index',
		'add' => 'useradmin/index',
		'edit' => 'useradmin/index',
		'delete' => 'useradmin/index',
		'view' => 'useradmin/index',
	);
	public $page = 'admin';

	protected $id;
	protected $page_offset;
	protected $user_admin_session;

	protected $list_headings = array(
		'',
		'Active',
		'Global',
		'Email (Username)',
		'Name',
		'Permission Groups',
		'Login Count',
		'Last Login',
	);
	protected $additional_user_info = array(
		array(
			'name' => 'Permission Groups',
			'relationship' => 'group',
			'alias' => 'group',
			'model' => 'group',
			'field_name' => 'group_id[]',
			'save_method' => 'save_through',
		),
	);

	public function before() {
		parent::before();

		$this->id = Request::instance()->param('id');
		$page_offset = cl4::get_param('page');

		if ( ! isset($this->session['useradmin'])) {
			$this->session['useradmin'] = array(
				'page_offset' => 0,
			);
		}
		$this->user_admin_session =& $this->session['useradmin'];

		if ($page_offset !== NULL) $this->user_admin_session['page_offset'] = intval($page_offset);
		$this->page_offset = $this->user_admin_session['page_offset'];

		if ($this->auto_render) {
			$this->template->styles['css/admin.css'] = 'screen';
			$this->template->styles['css/dbadmin.css'] = 'screen';
		}
	} // function before

	public function action_index() {
		$page_max_rows = 20;

		$offset = $this->page_offset;
		if ($offset > 0) {
			// subtract 1 because the first page_offset really by 0, but is passed as 1
			--$offset;
		}

		$user = ORM::factory('user_admin')
			->set_options(array('mode' => 'view'))
			->limit($page_max_rows)
			->offset($offset * $page_max_rows);

		$users = $user->find_all();
		$user_count = $user->count_all();

		// create the pagination object
		$pagination = Pagination::factory(array(
			'group' => 'default',
			'total_items'    => $user_count, // get the total number of records
			'items_per_page' => $page_max_rows,
			'current_page' => array(
				'page' => $this->page_offset,
			),
		));
		// track the records on page for display purposes
		//$items_on_page = $pagination->get_items_on_page();

		$table_options = array(
			'table_attributes' => array(
				'class' => 'cl4_content',
			),
			'heading' => $this->list_headings,
		);

		$table = new HTMLTable($table_options);

		foreach ($users as $user) {
			$user->set_mode('view');
			$table->add_row($this->get_list_row($user));
		} // foreach

		$this->template->body_html = View::factory('useradmin/user_list')
			->set('user_list', $table->get_html())
			->set('nav_html', $pagination->render());
	} // function action_index

	protected function get_list_row($user) {
		return array(
			$this->get_list_row_links($user),
			$user->get_field('active_flag'),
			$user->get_field('global_flag'),
			$user->get_field('username'),
			$user->get_field('first_name') . ' ' . $user->get_field('last_name'),
			$user->group->group_concat(),
			$user->get_field('login_count'),
			$user->get_field('last_login'),
		);
	}

	protected function get_list_row_links($user) {
		$id = $user->id;

		$first_col = HTML::anchor(Request::instance()->uri(array('action' => 'view', 'id' => $id)), '&nbsp;', array(
			'title' => __('View this user'),
			'class' => 'cl4_view',
		));

		$first_col .= HTML::anchor(Request::instance()->uri(array('action' => 'edit', 'id' => $id)), '&nbsp;', array(
			'title' => __('Edit this user'),
			'class' => 'cl4_edit',
		));

		$first_col .= HTML::anchor(Request::instance()->uri(array('action' => 'delete', 'id' => $id)), '&nbsp;', array(
			'title' => __('Delete this user'),
			'class' => 'cl4_delete',
		));

		$first_col .= HTML::anchor(Request::instance()->uri(array('action' => 'add', 'id' => $id)), '&nbsp;', array(
			'title' => __('Copy this user'),
			'class' => 'cl4_add',
		));

		$first_col .= HTML::anchor(Request::instance()->uri(array('action' => 'email_password', 'id' => $id)), '&nbsp;', array(
			'title' => __('Email a new random password to this user'),
			'class' => 'cl4_mail',
		));

		return $first_col;
	}

	public function action_view() {
		try {
			if ( ! ($this->id > 0)) {
				throw new Kohana_Exception('No ID received for view');
			}

			$this->template->body_html = View::factory('useradmin/user_view')
				->bind('user', $user);

			$user = ORM::factory('user_admin', $this->id)
				->set_mode('view')
				->set_option('get_view_view_file', 'useradmin/user_view_form')
				->set_option('additional_view_data', array('additional_user_info' => $this->additional_user_info));
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('useradmin', 'error_viewing', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	} // function action_view

	/**
	* Display an add form or add (save) a new record
	*/
	public function action_add() {
		try {
			$user = ORM::factory('user_admin', $this->id)
				->set_mode('add')
				->set_option('get_form_view_file', 'useradmin/user_edit_form')
				->set_option('additional_view_data', array('additional_user_info' => $this->additional_user_info));

			if ( ! empty($_POST)) {
				$this->save_user($user);
			}

			if ( ! empty($this->id)) {
				$user->set_option('form_action', URL::site(Request::current()->uri(array('id' => NULL))) . URL::query());
			}

			$this->template->body_html = View::factory('useradmin/user_edit')
				->bind('user', $user);
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_add', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	} // function action_add

	/**
	* Display an edit form for a record or update (save) an existing record
	*/
	public function action_edit() {
		try {
			$user = ORM::factory('user_admin', $this->id)
				->set_mode('edit')
				->set_option('get_form_view_file', 'useradmin/user_edit_form')
				->set_option('additional_view_data', array('additional_user_info' => $this->additional_user_info));

			if ( ! empty($_POST)) {
				$this->save_user($user);
			} // if

			$this->template->body_html = View::factory('useradmin/user_edit')
				->bind('user', $user);
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		} // try
	} // function action_edit

	/**
	* Saves the user record, including teams and permission groups
	*
	* @param  ORM  $user
	*/
	protected function save_user($user) {
		try {
			// validate the post data against the model
			$validation = $user->save_values()->check();

			if ($validation === TRUE) {
				$new_password = cl4::get_param_array(array('c_record', 'user', 0, 'password'), FALSE);
				$new_password = (empty($new_password) ? FALSE : $new_password);
				$new_user = ! ($user->id > 0); // false if this is not a new user

				// save the record
				if ($user->save()->saved()) {
					$this->user_additional_save($user);

					// now save the additional information
					foreach ($this->additional_user_info as $_additional) {
						$save_method = $_additional['save_method'];
						$user->$save_method($_additional['alias'], str_replace('[]', '', $_additional['field_name']));
					}

					$send_email = cl4::get_param('send_email', FALSE);
					if ($send_email) {
						$mail = new Mail();
						$mail->IsHTML();
						$mail->add_user($user->id);
						$mail->Subject = SHORT_NAME . ' Login Information';

						// provide a link to the user including their username
						$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

						$mail->Body = View::factory('useradmin/' . ($new_user ? 'new_account_email' : 'account_update_email'))
							->set('app_name', LONG_NAME)
							->set('username', $user->username)
							->set('password', $new_password)
							->set('url', $url)
							->set('admin_email', ADMIN_EMAIL);

						$mail->Send();

						Message::add(__(Kohana::message('useradmin', 'email_account_info')), Message::$notice);
					}

					Message::message('cl4admin', 'item_saved', NULL, Message::$notice);
					$this->redirect_to_index();
				} else {
					Message::message('cl4admin', 'item_may_have_not_saved', NULL, Message::$error);
				} // if
			} else {
				Message::message('cl4admin', 'values_not_valid', array(
					':validate_errors' => Message::add_validate_errors($user->validate(), 'user')
				), Message::$error);
			}
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'problem_saving', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		} // try
	} // function save_user

	/**
	* Run inside save_user() after the user record is saved, allowing for saving additional information quickly
	* By default, nothing extra is saved
	*
	* @param mixed $user
	*/
	protected function user_additional_save($user) { }

	/**
	* Delete a record with a confirm first
	*/
	public function action_delete() {
		try {
			if ( ! ($this->id > 0)) {
				Message::message('cl4admin', 'no_id', NULL, Message::$error);
				$this->redirect_to_index();
			} // if

			if ( ! empty($_POST)) {
				// see if they want to delete the item
				if (strtolower($_POST['cl4_delete_confirm']) == 'yes') {
					try {
						$user = ORM::factory('user_admin', $this->id);
						if ($user->delete() == 0) {
							Message::message('cl4admin', 'no_item_deleted', NULL, Message::$error);
						} else {
							Message::add('The user was deleted.', Message::$notice);
							Message::message('cl4admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
						} // if
					} catch (Exception $e) {
						cl4::exception_handler($e);
						Message::message('cl4admin', 'error_deleting', NULL, Message::$error);
						if ( ! cl4::is_dev()) $this->redirect_to_index();
					}
				} else {
					Message::message('cl4admin', 'item_not_deleted', NULL, Message::$notice);
				}

				$this->redirect_to_index();

			} else {
				// the confirmation form goes in the messages
				Message::add(View::factory('useradmin/confirm_delete'));

				$this->action_view();
			}
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_delete', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	} // function action_delete

	public function action_email_password() {
		try {
			if ( ! ($this->id > 0)) {
				Message::message('cl4admin', 'no_id', NULL, Message::$error);
				$this->redirect_to_index();
			} // if

			$new_password = cl4_Auth::generate_password();

			$user = ORM::factory('user', $this->id)
				->values(array(
					'password' => $new_password,
					'force_update_password_flag' => 1,
					'failed_login_count' => 0,
					'last_failed_login' => 0,
				))
				->save();

			$mail = new Mail();
			$mail->IsHTML();
			$mail->add_user($user->id);
			$mail->Subject = SHORT_NAME . ' Login Information';

			// provide a link to the user including their username
			$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

			$mail->Body = View::factory('useradmin/login_information_email')
				->set('app_name', LONG_NAME)
				->set('username', $user->username)
				->set('password', $new_password)
				->set('url', $url)
				->set('admin_email', ADMIN_EMAIL);

			$mail->Send();

			Message::add(__(Kohana::message('useradmin', 'email_password_sent')), Message::$notice);

			$this->redirect_to_index();

		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('useradmin', 'error_preparing_email', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	}

	/**
	* Cancel the current action by redirecting back to the index action
	*/
	public function action_cancel() {
		// add a notice to be displayed
		Message::message('cl4admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_index();
	} // function

	public function redirect_to_index() {
		Request::instance()->redirect('/' . Route::get(Route::name(Request::instance()->route))->uri());
	}
} // class Controller_UserAdmin