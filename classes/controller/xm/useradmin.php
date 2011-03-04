<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_UserAdmin extends Controller_Base {
	public $auth_required = TRUE;
	public $secure_actions = array(
		'index' => 'useradmin/index',
		'add' => 'useradmin/index',
		'edit' => 'useradmin/index',
		'delete' => 'useradmin/index',
		'view' => 'useradmin/index',
		'groups' => 'useradmin/index',
		'add_group' => 'useradmin/index',
		'edit_group' => 'useradmin/index',
		'cancel_group' => 'useradmin/index',
	);
	public $page = 'admin';

	protected $id;
	protected $page_offset;
	protected $user_admin_session;

	protected $list_headings = array(
		'',
		'Active',
		'Email (Username)',
		'Name',
		'Permission Groups',
		'Login Count',
		'Last Login',
	);
	protected $group_list_headings = array(
		'',
		'Name',
		'Description',
	);

	public function before() {
		parent::before();

		$this->id = Request::current()->param('id');
		$page_offset = cl4::get_param('page');

		if ( ! isset($this->session['useradmin'])) {
			$this->session['useradmin'] = array(
				'page_offset' => 0,
			);
		}
		$this->user_admin_session = & $this->session['useradmin'];

		if ($page_offset !== NULL) $this->user_admin_session['page_offset'] = intval($page_offset);
		$this->page_offset = $this->user_admin_session['page_offset'];

		$this->add_admin_css();

		if ($this->request->action() == 'groups') {
			$page_title = 'Groups';
		} else if ($this->request->action() == 'index') {
			$page_title = 'Users';
		} else {
			$page_title = NULL;
		}

		if ( ! empty($page_title)) {
			$this->template->pre_message = View::factory('useradmin/menu')
				->set('page_title', $page_title);
		}
	} // function before

	/**
	* Adds the CSS for cl4admin
	*/
	protected function add_admin_css() {
		if ($this->auto_render) {
			$this->template->styles['css/admin.css'] = NULL;
			$this->template->styles['css/dbadmin.css'] = NULL;
			$this->template->styles['css/useradmin.css'] = NULL;
		}

		return $this;
	} // function add_admin_css

	public function add_template_js() {
		parent::add_template_js();

		$this->template->scripts['useradmin'] = 'js/useradmin.js';

		return $this;
	}

	public function action_index() {
		$page_max_rows = 20;

		$offset = $this->page_offset;
		if ($offset > 0) {
			// subtract 1 because the first page_offset really by 0, but is passed as 1
			--$offset;
		}

		$user = $this->get_user_orm_list($page_max_rows, $offset);

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

	/**
	* Returns an Model_User_Admin (ORM) to retrieve the users
	*
	* @param   int  $page_max_rows
	* @param   int  $offset
	* @return  Model_User_Admin
	*/
	protected function get_user_orm_list($page_max_rows, $offset) {
		return ORM::factory('user_admin')
			->set_options(array('mode' => 'view'))
			->limit($page_max_rows)
			->offset($offset * $page_max_rows);
	}

	protected function get_list_row($user) {
		return array(
			$this->get_list_row_links($user),
			$user->get_field('active_flag'),
			$user->get_field('username'),
			$user->get_field('first_name') . ' ' . $user->get_field('last_name'),
			$user->group->group_concat(),
			$user->get_field('login_count'),
			$user->get_field('last_login'),
		);
	}

	protected function get_list_row_links($user) {
		$id = $user->id;

		$first_col = HTML::anchor(Request::current()->uri(array('action' => 'view', 'id' => $id)), '&nbsp;', array(
			'title' => __('View this user'),
			'class' => 'cl4_view',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'edit', 'id' => $id)), '&nbsp;', array(
			'title' => __('Edit this user'),
			'class' => 'cl4_edit',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'delete', 'id' => $id)), '&nbsp;', array(
			'title' => __('Delete this user'),
			'class' => 'cl4_delete',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'add', 'id' => $id)), '&nbsp;', array(
			'title' => __('Copy this user'),
			'class' => 'cl4_add',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'email_password', 'id' => $id)), '&nbsp;', array(
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
				->set_option('get_view_view_file', 'useradmin/user_view_form');
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
				->set_option('get_form_view_file', 'useradmin/user_edit_form');

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
				->set_option('get_form_view_file', 'useradmin/user_edit_form');

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
			// save the post data
			$user->save_values()->save();

			$this->user_additional_save($user);

			$send_email = cl4::get_param('send_email', FALSE);
			if ($send_email) {
				$new_password = cl4::get_param_array(array('c_record', 'user', 0, 'password'), FALSE);
				$new_password = (empty($new_password) ? FALSE : $new_password);
				$new_user = ! ($user->id > 0); // false if this is not a new user

				$mail = new Mail();
				$mail->IsHTML();
				$mail->add_user($user->id);
				$mail->Subject = SHORT_NAME . ' Login Information';

				// provide a link to the user including their username
				$url = URL::site(Route::get('login')->uri(), Request::current()->protocol()) . '?' . http_build_query(array('username' => $user->username));

				$mail->Body = View::factory('useradmin/' . ($new_user ? 'new_account_email' : 'account_update_email'))
					->set('app_name', LONG_NAME)
					->set('username', $user->username)
					->set('password', $new_password)
					->set('url', $url)
					->set('support_email', Kohana::config('useradmin.support_email'));

				$mail->Send();

				Message::add(__(Kohana::message('useradmin', 'email_account_info')), Message::$notice);
			}

			Message::message('cl4admin', 'item_saved', NULL, Message::$notice);
			$this->redirect_to_index();

		} catch (ORM_Validation_Exception $e) {
			Message::message('cl4admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, '')
			), Message::$error);
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
	* @param  ORM  $user
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
			$url = URL::site(Route::get('login')->uri(), Request::current()->protocol()) . '?' . http_build_query(array('username' => $user->username));

			$mail->Body = View::factory('useradmin/login_information_email')
				->set('app_name', LONG_NAME)
				->set('username', $user->username)
				->set('password', $new_password)
				->set('url', $url)
				->set('support_email', Kohana::config('useradmin.support_email'));

			$mail->Send();

			Message::add(__(Kohana::message('useradmin', 'email_password_sent')), Message::$notice);

			$this->redirect_to_index();

		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('useradmin', 'error_preparing_email', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	} // function action_email_password

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
		Request::current()->redirect(Route::get(Route::name(Request::current()->route()))->uri());
	}

	public function action_groups() {
		$group = ORM::factory('group')
			->set_options(array('mode' => 'view'));
		$groups = $group->find_all();
		$group_count = $group->count_all();

		$table_options = array(
			'table_attributes' => array(
				'class' => 'cl4_content',
			),
			'heading' => $this->group_list_headings,
		);

		$table = new HTMLTable($table_options);

		foreach ($groups as $group) {
			$group->set_mode('view');
			$table->add_row($this->get_group_list_row($group));
		} // foreach

		$this->template->body_html = View::factory('useradmin/group_list')
			->set('group_list', $table->get_html())
			->set('group_count', $group_count);
	} // function action_groups

	protected function get_group_list_row($group) {
		return array(
			$this->get_group_list_row_links($group),
			$group->get_field('name'),
			$group->get_field('description'),
		);
	}

	protected function get_group_list_row_links($group) {
		$id = $group->id;

		$first_col = HTML::anchor(Request::current()->uri(array('action' => 'edit_group', 'id' => $id)), '&nbsp;', array(
			'title' => __('Edit this group'),
			'class' => 'cl4_edit',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'delete_group', 'id' => $id)), '&nbsp;', array(
			'title' => __('Delete this group'),
			'class' => 'cl4_delete',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'add_group', 'id' => $id)), '&nbsp;', array(
			'title' => __('Copy this group'),
			'class' => 'cl4_add',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'group_permissions', 'id' => $id)), '&nbsp;', array(
			'title' => __('Edit the permissions for this group'),
			'class' => 'cl4_lock',
		));

		$first_col .= HTML::anchor(Request::current()->uri(array('action' => 'group_users', 'id' => $id)), '&nbsp;', array(
			'title' => __('Edit the users that have this permission group'),
			'class' => 'cl4_contact2',
		));

		return $first_col;
	}

	/**
	* Display an add form or add (save) a new record
	*/
	public function action_add_group() {
		try {
			$group = ORM::factory('group', $this->id)
				->set_mode('add')
				->set_option('cancel_button_attributes', array(
					'data-cl4_link' => URL::site(Route::get('useradmin')->uri(array('action' => 'cancel_group'))),
				));

			if ( ! empty($_POST)) {
				$this->save_group($group);
			}

			if ( ! empty($this->id)) {
				$group->set_option('form_action', URL::site(Request::current()->uri(array('id' => NULL))) . URL::query());
			}

			$this->template->body_html = View::factory('useradmin/group_edit')
				->bind('group', $group);
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_add', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		}
	} // function action_add

	/**
	* Display an edit form for a record or update (save) an existing record
	*/
	public function action_edit_group() {
		try {
			$group = ORM::factory('group', $this->id)
				->set_mode('edit')
				->set_option('cancel_button_attributes', array(
					'data-cl4_link' => URL::site(Route::get('useradmin')->uri(array('action' => 'cancel_group'))),
				));

			if ( ! empty($_POST)) {
				$this->save_group($group);
			} // if

			$this->template->body_html = View::factory('useradmin/group_edit')
				->bind('group', $group);
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function action_edit

	/**
	* Saves the user record, including teams and permission groups
	*
	* @param  ORM  $user
	*/
	protected function save_group($group) {
		try {
			// save the post data
			$validation = $group->save_values()->save();

			Message::message('cl4admin', 'item_saved', NULL, Message::$notice);
			$this->redirect_to_group_list();

		} catch (ORM_Validation_Exception $e) {
			Message::message('cl4admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, '')
			), Message::$error);
		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'problem_saving', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function save_user

	/**
	* Cancel the current action by redirecting back to the groups action
	*/
	public function action_cancel_group() {
		// add a notice to be displayed
		Message::message('cl4admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_group_list();
	} // function

	public function redirect_to_group_list() {
		Request::current()->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'groups')));
	}

	public function action_group_permissions() {
		if ( ! empty($_POST)) {
			try {
				ORM::factory('group', $this->id)
					->save_through('permission', 'current_permissions', $save_through_counts);

				Message::add('The permissions for the group were updated' . $this->get_count_msg('permission', $save_through_counts), Message::$notice);
				$this->redirect_to_group_list();

			} catch (Exception $e) {
				cl4::exception_handler($e);
				Message::message('cl4admin', 'problem_saving', NULL, Message::$error);
				if ( ! cl4::is_dev()) $this->redirect_to_group_list();
			} // try
		} // if

		try {
			$group = ORM::factory('group', $this->id);

			$select_perm_id = 'permission.id';
			$select_perm_name = array(DB::expr("CONCAT_WS('', permission.name, ' (', permission.permission, ')')"), 'permission_name');

			$all_permissions = ORM::factory('permission')
				->select($select_perm_id)
				->select($select_perm_name)
				->find_all()
				->as_array('id', 'permission_name');

			$current_permissions = ORM::factory('group', $this->id)
				->permission
				->select($select_perm_id)
				->select($select_perm_name)
				->find_all()
				->as_array('id', 'permission_name');

			// remove all the current permissions from the all list
			foreach ($current_permissions as $perm_id => $perm_name) {
				if (isset($all_permissions[$perm_id])) {
					unset($all_permissions[$perm_id]);
				}
			}

			$available_perms_select = Form::select('available_permissions[]', $all_permissions, array(), array(
				'size' => 10,
				'class' => 'xm_permission_edit_select',
			));
			$current_perms_select = Form::select('current_permissions[]', $current_permissions, array(), array(
				'size' => 10,
				'class' => 'xm_permission_edit_select xm_include_in_save',
			));

			// now attempt to generate the permission group drop downs
			if (class_exists('Model_Permission_Group')) {
				$permission_groups = ORM::factory('permission_group')
					->find_all();

				if (count($permission_groups) > 0) {
					$perm_group_data = array();
					foreach ($permission_groups as $permission_group) {
						$permission_ids = $permission_group
							->permission_id
							->select('permission_group_permission.id')
							->find_all()
							->as_array(NULL, 'id');

						$perm_group_data[implode(',', $permission_ids)] = $permission_group->name;
					}

					$perm_group_select_add = Form::select('add_group_select', $perm_group_data, NULL, array(), array('select_one' => TRUE));
					$perm_group_select_remove = Form::select('remove_group_select', $perm_group_data, NULL, array(), array('select_one' => TRUE));
				} // if
			}

			$this->template->body_html = View::factory('useradmin/group_permission_edit')
				->bind('group', $group)
				->bind('available_perms_select', $available_perms_select)
				->bind('current_perms_select', $current_perms_select)
				->bind('permission_group_select_add', $perm_group_select_add)
				->bind('permission_group_select_remove', $perm_group_select_remove);

		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function action_group_permissions

	public function action_group_users() {
		if ( ! empty($_POST)) {
			try {
				ORM::factory('group', $this->id)
					->save_through('user', 'current_users', $save_through_counts);

				Message::add('The users in the group were updated' . $this->get_count_msg('user', $save_through_counts), Message::$notice);
				$this->redirect_to_group_list();

			} catch (Exception $e) {
				cl4::exception_handler($e);
				Message::message('cl4admin', 'problem_saving', NULL, Message::$error);
				if ( ! cl4::is_dev()) $this->redirect_to_group_list();
			} // try
		} // if

		try {
			$group = ORM::factory('group', $this->id);

			$select_user_id = 'user.id';
			$select_user_name = array(DB::expr("CONCAT_WS('', user.first_name, ' ', user.last_name)"), 'name');

			$all_users = ORM::factory('user')
				->select($select_user_id)
				->select($select_user_name)
				->find_all()
				->as_array('id', 'name');

			$current_users = ORM::factory('group', $this->id)
				->user
				->select($select_user_id)
				->select($select_user_name)
				->find_all()
				->as_array('id', 'name');

			// remove all the current permissions from the all list
			foreach ($current_users as $user_id => $user_name) {
				if (isset($all_users[$user_id])) {
					unset($all_users[$user_id]);
				}
			}

			$available_users_select = Form::select('available_users[]', $all_users, array(), array(
				'size' => 10,
				'class' => 'xm_permission_edit_select',
			));
			$current_users_select = Form::select('current_users[]', $current_users, array(), array(
				'size' => 10,
				'class' => 'xm_permission_edit_select xm_include_in_save',
			));

			$this->template->body_html = View::factory('useradmin/group_user_edit')
				->bind('group', $group)
				->bind('available_users_select', $available_users_select)
				->bind('current_users_select', $current_users_select);

		} catch (Exception $e) {
			cl4::exception_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function action_group_users

	/**
	* Generate a message for the user regarding the records that were removed, added and kept
	*
	* @param  string  $name
	* @param  array   $counts
	*
	* @return  string
	*/
	protected function get_count_msg($name, $counts) {
		$count_msg = '';
		if ($counts['removed'] > 0) {
			$count_msg .= $counts['removed'] . ' ' . $name . Text::s($counts['removed']) . ' removed';
		}
		if ($counts['added'] > 0) {
			$count_msg .= ( ! empty($count_msg) ? ', ' : '') . $counts['added'] . ' ' . $name . Text::s($counts['added']) . ' added';
		}
		if ($counts['kept'] > 0) {
			$count_msg .= ( ! empty($count_msg) ? ', ' : '') . $counts['kept'] . ' ' . $name . Text::s($counts['kept']) . ' stayed the same';
		}
		if ( ! empty($count_msg)) {
			$count_msg = ': ' . $count_msg . '.';
		} else {
			$count_msg = '.';
		}

		return $count_msg;
	} // function get_count_msg
} // class Controller_UserAdmin