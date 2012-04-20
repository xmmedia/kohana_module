<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_UserAdmin extends Controller_Base {
	public $auth_required = TRUE;
	public $secure_actions = array(
		'index' => 'useradmin/index',
		'add' => 'useradmin/add',
		'edit' => 'useradmin/edit',
		'delete' => 'useradmin/delete',
		'view' => 'useradmin/index',
		'email_password' => 'useradmin/email_password',
		'cancel' => 'useradmin/index',
		'groups' => 'useradmin/group/index',
		'add_group' => 'useradmin/group/add',
		'edit_group' => 'useradmin/group/edit',
		'delete_group' => 'useradmin/group/delete',
		'view_group' => 'useradmin/group/index',
		'group_permissions' => 'useradmin/group/permissions',
		'group_users' => 'useradmin/group/users',
		'cancel_group' => 'useradmin/group/index',
	);
	public $page = 'admin';

	protected $id;
	protected $page_offset;
	protected $sort_column;
	protected $sort_direction;
	protected $user_admin_session;

	protected $list_headings = array(
		'' => '',
		'user_admin.active_flag' => 'Active',
		'user_admin.username' => 'Email (Username)',
		'user_admin.last_name,user_admin.first_name' => 'Name',
		'' => 'Permission Groups',
		'user_admin.login_count' => 'Login Count',
		'user_admin.last_login' => 'Last Login',
	);
	protected $group_list_headings = array(
		'',
		'Name',
		'Description',
	);
	protected $page_max_rows = 30;

	/**
	* @var  array  Stores the group ids and names that the user can edit
	* NULL by default
	*/
	protected $allowed_groups;

	public function before() {
		parent::before();

		$this->id = Request::current()->param('id');
		$page_offset = Arr::get($_REQUEST, 'page');
		$sort_column = Arr::get($_REQUEST, 'sort_column');
		$sort_direction = Arr::get($_REQUEST, 'sort_direction');



		if ( ! isset($this->session['useradmin'])) {
			$this->session['useradmin'] = array(
				'users' => array(
					'page_offset' => 0,
					'sort_column' => NULL,
					'sort_direction' => NULL,
				),
				'groups' => array(
					'page_offset' => 0,
				),
			);
		}
		$this->user_admin_session = & $this->session['useradmin'];

		$this->add_admin_css();

		if ($this->request->action() == 'groups') {
			$page_title = 'Groups';

			if ($page_offset !== NULL) $this->user_admin_session['groups']['page_offset'] = intval($page_offset);
			$this->page_offset = $this->user_admin_session['groups']['page_offset'];

		} else if ($this->request->action() == 'index') {
			$page_title = 'Users';

			if ($page_offset !== NULL) $this->user_admin_session['users']['page_offset'] = intval($page_offset);
			$this->page_offset = $this->user_admin_session['users']['page_offset'];

			// if the sort column in is the parameter but it's empty, then erase the sort
			// because the user has clicked the column a third time
			if ($sort_column == '' && $sort_column !== NULL) {
				$this->user_admin_session['users']['sort_column'] = NULL;
				$this->user_admin_session['users']['sort_direction'] = NULL;
			} else {
				if ($sort_column !== NULL) $this->user_admin_session['users']['sort_column'] = strtolower($sort_column);
				if ($sort_direction !== NULL) $this->user_admin_session['users']['sort_direction'] = strtoupper($sort_direction);
			}

			$this->sort_column = $this->user_admin_session['users']['sort_column'];
			$this->sort_direction = $this->user_admin_session['users']['sort_direction'];
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
			$this->template->styles['xm/css/useradmin.css'] = NULL;
		}

		return $this;
	} // function add_admin_css

	public function add_template_js() {
		parent::add_template_js();

		$this->template->scripts['useradmin'] = 'xm/js/useradmin.js';

		return $this;
	}

	public function action_index() {
		$offset = $this->page_offset;
		if ($offset > 0) {
			// subtract 1 because the first page_offset really by 0, but is passed as 1
			--$offset;
		}

		$user = $this->get_user_orm_list($this->page_max_rows, $offset);

		$users = $user->find_all();
		$user_count = $user->count_all();

		// create the pagination object
		$pagination = Pagination::factory(array(
			'group' => 'default',
			'total_items'    => $user_count, // get the total number of records
			'items_per_page' => $this->page_max_rows,
			'current_page' => array(
				'page' => $this->page_offset,
			),
		));

		$table_options = array(
			'table_attributes' => array(
				'class' => 'cl4_content',
			),
			'heading' => array(),
		);

		$sort_url = $this->request->route()->uri();
		$i = 0;
		foreach ($this->list_headings as $sort_column => $heading) {
			// if there is no sort column, then this column be used in sorting (ie, permission groups)
			if (empty($sort_column)) {
				$table_options['heading'][] = HTML::chars($heading);
			} else {
				if ($sort_column != $this->sort_column) {
					$sort_query = 'sort_column=' . $sort_column . '&sort_direction=ASC';
				// the current column is the column being sorted
				} else {
					if ($this->sort_direction == 'ASC') {
						$sort_query = 'sort_column=' . $sort_column . '&sort_direction=DESC';
					} else if ($this->sort_direction == 'DESC') {
						$sort_query = 'sort_column=&sort_direction=';
					}
				}

				$table_options['heading'][] = HTML::anchor($sort_url . '?' . $sort_query, HTML::chars($heading));
				if ($sort_column == $this->sort_column) {
					$table_options['sort_column'] = $i;
					$table_options['sort_order'] = $this->sort_direction;
				}
			}

			++ $i;
		}

		$table = new HTMLTable($table_options);

		foreach ($users as $user) {
			$user->set_mode('view');
			$table->add_row($this->get_list_row($user));
		} // foreach

		$this->template->body_html = View::factory('useradmin/user_list')
			->set('user_list', $table->get_html())
			->set('nav_html', $pagination->render())
			->set('list_buttons', $this->get_user_list_buttons());
	} // function action_index

	/**
	* Returns an Model_User_Admin (ORM) to retrieve the users
	*
	* @param   int  $page_max_rows
	* @param   int  $offset
	* @return  Model_User_Admin
	*/
	protected function get_user_orm_list($page_max_rows, $offset) {
		$users = ORM::factory('user_admin')
			->set_options(array('mode' => 'view'))
			->limit($page_max_rows)
			->offset($offset * $page_max_rows);

		if (empty($this->sort_column)) {
			$users->order_by('user_admin.last_name')
				->order_by('user_admin.first_name');
		} else {
			$sort_columns = explode(',', $this->sort_column);
			foreach ($sort_columns as $sort_column) {
				$users->order_by($sort_column, $this->sort_direction);
			}
		}

		return $users;
	} // function get_user_orm_list

	/**
	 * Return an array of the columns for the user list.
	 * The array should be ready to be passed directly to HTMLTable.
	 *
	 * @param  Model_User  $user  The user model.
	 * @return  array
	 */
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
	} // function get_list_row

	/**
	 * Returns the HTML for the first column of the user list.
	 *
	 * @param  Model_User  $user  The user model; usually for the ID.
	 * @return  string
	 */
	protected function get_list_row_links($user) {
		$id = $user->id;

		$first_col = HTML::anchor($this->request->route()->uri(array('action' => 'view', 'id' => $id)), '&nbsp;', array(
			'title' => __('View this user'),
			'class' => 'cl4_view',
		));

		if (Auth::instance()->allowed('useradmin/edit')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'edit', 'id' => $id)), '&nbsp;', array(
				'title' => __('Edit this user'),
				'class' => 'cl4_edit',
			));
		}

		if (Auth::instance()->allowed('useradmin/delete')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'delete', 'id' => $id)), '&nbsp;', array(
				'title' => __('Delete this user'),
				'class' => 'cl4_delete',
			));
		}

		if (Auth::instance()->allowed('useradmin/add')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'add', 'id' => $id)), '&nbsp;', array(
				'title' => __('Copy this user'),
				'class' => 'cl4_add',
			));
		}

		if (Auth::instance()->allowed('useradmin/email_password')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'email_password', 'id' => $id)), '&nbsp;', array(
				'title' => __('Email a new random password to this user'),
				'class' => 'cl4_mail',
			));
		}

		return $first_col;
	} // function get_list_row_links

	/**
	 * Returns an array with the HTML buttons to display at the top of the user list.
	 * This array is passed to the user_list view and imploded.
	 *
	 * @return  array
	 */
	protected function get_user_list_buttons() {
		$list_buttons = array(
			Form::submit(NULL, 'Add New User', array('class' => 'cl4_button_link cl4_list_button', 'data-cl4_link' => URL::site($this->request->route()->uri(array('action' => 'add'))))),
		);
		if ( ! empty($this->sort_column)) {
			$list_buttons[] = Form::input_button(NULL, 'Clear Sort', array('class' => 'cl4_button_link cl4_list_button', 'data-cl4_link' => URL::site($this->request->route()->uri() . '?sort_column=&sort_direction=')));
		}
		return $list_buttons;
	} // function get_user_list_buttons

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
			Kohana_Exception::caught_handler($e);
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

			$this->set_user_permission_edit($user);

			if ( ! empty($_POST)) {
				$this->save_user($user);
			} else {
				// don't show the failed login count on add as it should default to 0
				$user->set_table_columns('failed_login_count', 'edit_flag', FALSE);
			}

			if ( ! empty($this->id)) {
				$user->set_option('form_action', URL::site(Request::current()->route()->uri(array('action' => 'add'))) . URL::query());
			}

			$this->template->body_html = View::factory('useradmin/user_edit')
				->bind('user', $user);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
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

			// if the user can edit permissions groups, but doesn't have access to all groups
			// look for groups that they don't have access to but are on this user and add a message
			if ($this->set_user_permission_edit($user) && ! Auth::instance()->allowed('useradmin/user/group/*')) {
				$user_groups = $user->group->find_all()->as_array('id', 'name');
				$other_groups = '';
				$other_count = 0;
				foreach ($user_groups as $group_id => $group_name) {
					if ( ! Auth::instance()->allowed('useradmin/user/group/' . $group_id)) {
						if ($other_count > 0) $other_groups .= ', ';
						$other_groups .= $group_name;
						++$other_count;
					}
				}
				if ($other_count > 0) {
					Message::message('useradmin', 'other_groups', array(':other_groups' => HTML::chars($other_groups)), Message::$notice);
				}
			} // if

			if ( ! empty($_POST)) {
				$this->save_user($user);
			} // if

			$this->template->body_html = View::factory('useradmin/user_edit')
				->bind('user', $user);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		} // try
	} // function action_edit

	/**
	* Sets the edit_flag or array of data for the group relationship inside the user admin model
	* so that users can't edit groups that they don't have permissions to.
	*
	* @param  ORM  $user  The other model
	*
	* @return  boolean  Returns FALSE when the user doesn't have permission to edit any groups; TRUE when they have permission to 1 or more groups, possibly all
	*/
	protected function set_user_permission_edit($user) {
		// see if they have permission to edit the permissions of users
		if ( ! Auth::instance()->allowed('useradmin/edit/permissions')) {
			$user->set_has_many('group', 'edit_flag', FALSE);
			return FALSE;

		// if they don't have permission to add all groups, then check for the groups they do have permissions to
		} else if ( ! Auth::instance()->allowed('useradmin/user/group/*')) {
			$allowed_groups = $this->allowed_groups();
			if ( ! empty($allowed_groups)) {
				$user->set_has_many('group', 'source.source', 'array')
					->set_has_many('group', 'source.data', $allowed_groups);
				return TRUE;
			} else {
				$user->set_has_many('group', 'edit_flag', FALSE);
				return FALSE;
			}
		} else if (Auth::instance()->allowed('useradmin/user/group/*')) {
			return TRUE;
		} // if
	} // function set_user_permission_edit

	/**
	* Returns an array of groups that the user has permission to edit
	* group id => group name
	*
	* @return  array
	*/
	protected function allowed_groups() {
		if ($this->allowed_groups === NULL) {
			$this->allowed_groups = array();
			foreach (ORM::factory('group')->find_all() as $group) {
				if (Auth::instance()->allowed('useradmin/user/group/' . $group->pk())) {
					$this->allowed_groups[$group->pk()] = $group->name;
				}
			}
		}

		return $this->allowed_groups;
	} // function allowed_groups

	/**
	* Saves the user record, including permission groups
	*
	* @param  ORM  $user
	*/
	protected function save_user($user) {
		try {
			$post = $_POST;

			// the user is allowed to change the groups on a user, but does not have access to all groups
			if ($this->set_user_permission_edit($user) && ! Auth::instance()->allowed('useradmin/user/group/*')) {
				// look in the post for any of the groups that the user is not allowed to add users to (security check)
				$allowed_groups = $this->allowed_groups();
				$selected_groups = Arr::path($post, 'c_record.group', array());
				foreach ($selected_groups as $key => $group_id) {
					if ( ! isset($allowed_groups[$group_id])) {
						unset($selected_groups[$key]);
					}
				}

				// re-add any groups that the user doesn't have permissions to
				$other_groups = ORM::factory('user_group')
					->where('user_group.group_id', 'NOT IN', array_keys($allowed_groups))
					->where('user_group.user_id', '=', $user->id)
					->find_all();
				if (count($other_groups) > 0) {
					foreach ($other_groups as $user_group) {
						$selected_groups[] = $user_group->group_id;
					}
				}

				$post['c_record']['group'] = $selected_groups;
			} // if

			// save the post data
			$user->save_values($post)->save();

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
				$editing_user = Auth::instance()->get_user();
				if (Valid::email($editing_user->username)) {
					$mail->AddReplyTo($editing_user->username, $editing_user->first_name . ' ' . $editing_user->last_name);
				}

				// provide a link to the user including their username
				$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

				$mail->Body = View::factory('useradmin/' . ($new_user ? 'new_account_email' : 'account_update_email'))
					->set('app_name', LONG_NAME)
					->set('username', $user->username)
					->set('password', $new_password)
					->set('url', $url)
					->set('support_email', Kohana::$config->load('useradmin.support_email'));

				$mail->Send();

				Message::message('useradmin', 'email_account_info', array(), Message::$notice);
			}

			Message::message('cl4admin', 'item_saved', NULL, Message::$notice);
			$this->redirect_to_index();

		} catch (ORM_Validation_Exception $e) {
			Message::message('cl4admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, '')
			), Message::$error);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
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
							Message::message('useradmin', 'user_deleted', array(), Message::$notice);
							Message::message('cl4admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
						} // if
					} catch (Exception $e) {
						Kohana_Exception::caught_handler($e);
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
			Kohana_Exception::caught_handler($e);
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
			$editing_user = Auth::instance()->get_user();
			if (Valid::email($editing_user->email)) {
				$mail->AddReplyTo($editing_user->email, $editing_user->first_name . ' ' . $editing_user->last_name);
			}

			// provide a link to the user including their username
			$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

			$mail->Body = View::factory('useradmin/login_information_email')
				->set('app_name', LONG_NAME)
				->set('username', $user->username)
				->set('password', $new_password)
				->set('url', $url)
				->set('support_email', Kohana::$config->load('useradmin.support_email'));

			$mail->Send();

			Message::message('useradmin', 'email_password_sent', array(), Message::$notice);

			$this->redirect_to_index();

		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
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
	}

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

		$first_col = HTML::anchor($this->request->route()->uri(array('action' => 'view_group', 'id' => $id)), '&nbsp;', array(
			'title' => __('View this user'),
			'class' => 'cl4_view',
		));

		if (Auth::instance()->allowed('useradmin/group/edit')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'edit_group', 'id' => $id)), '&nbsp;', array(
				'title' => __('Edit this group'),
				'class' => 'cl4_edit',
			));
		}

		if (Auth::instance()->allowed('useradmin/group/delete')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'delete_group', 'id' => $id)), '&nbsp;', array(
				'title' => __('Delete this group'),
				'class' => 'cl4_delete',
			));
		}

		if (Auth::instance()->allowed('useradmin/group/add')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'add_group', 'id' => $id)), '&nbsp;', array(
				'title' => __('Copy this group'),
				'class' => 'cl4_add',
			));
		}

		if (Auth::instance()->allowed('useradmin/group/permissions')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'group_permissions', 'id' => $id)), '&nbsp;', array(
				'title' => __('Edit the permissions for this group'),
				'class' => 'cl4_lock',
			));
		}

		if (Auth::instance()->allowed('useradmin/group/users')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'group_users', 'id' => $id)), '&nbsp;', array(
				'title' => __('Edit the users that have this permission group'),
				'class' => 'cl4_contact2',
			));
		}

		return $first_col;
	} // function get_group_list_row_links

	public function action_view_group() {
		try {
			if ( ! ($this->id > 0)) {
				throw new Kohana_Exception('No ID received for view');
			}

			$this->template->body_html = View::factory('useradmin/group_view')
				->bind('group', $group);

			$group = ORM::factory('group', $this->id)
				->set_mode('view')
				->set_option('get_view_view_file', 'useradmin/group_view_form')
				->set_option('display_buttons', FALSE);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::message('useradmin', 'error_viewing', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_index();
		}
	} // function action_view_group

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
				$group->set_option('form_action', URL::site($this->request->route()->uri(array('action' => $this->request->action()))) . URL::query());
			}

			$this->template->body_html = View::factory('useradmin/group_edit')
				->bind('group', $group);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
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
			Kohana_Exception::caught_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function action_edit

	/**
	* Saves the group record
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
			Kohana_Exception::caught_handler($e);
			Message::message('cl4admin', 'problem_saving', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function save_user

	/**
	* Delete a record with a confirm first
	*/
	public function action_delete_group() {
		try {
			if ( ! ($this->id > 0)) {
				Message::message('cl4admin', 'no_id', NULL, Message::$error);
				$this->redirect_to_group_list();
			} // if

			if ( ! empty($_POST)) {
				// see if they want to delete the item
				if (strtolower($_POST['cl4_delete_confirm']) == 'yes') {
					try {
						$group = ORM::factory('group', $this->id);
						if ($group->delete() == 0) {
							Message::message('cl4admin', 'no_item_deleted', NULL, Message::$error);
						} else {
							Message::message('useradmin', 'user_deleted', array(), Message::$notice);
							Message::message('cl4admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
						} // if
					} catch (Exception $e) {
						Kohana_Exception::caught_handler($e);
						Message::message('cl4admin', 'error_deleting', NULL, Message::$error);
						if ( ! cl4::is_dev()) $this->redirect_to_group_list();
					}
				} else {
					Message::message('cl4admin', 'item_not_deleted', NULL, Message::$notice);
				}

				$this->redirect_to_group_list();

			} else {
				// the confirmation form goes in the messages
				Message::add(View::factory('useradmin/confirm_delete'));

				$this->action_view_group();
			}
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::message('cl4admin', 'error_preparing_delete', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		}
	} // function action_delete

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

				Message::message('useradmin', 'group_permissions_updated', array(':count' => $this->get_count_msg('permission', $save_through_counts)), Message::$notice);
				$this->redirect_to_group_list();

			} catch (Exception $e) {
				Kohana_Exception::caught_handler($e);
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
			Kohana_Exception::caught_handler($e);
			Message::message('cl4admin', 'error_preparing_edit', NULL, Message::$error);
			if ( ! cl4::is_dev()) $this->redirect_to_group_list();
		} // try
	} // function action_group_permissions

	public function action_group_users() {
		if ( ! empty($_POST)) {
			try {
				ORM::factory('group', $this->id)
					->save_through('user', 'current_users', $save_through_counts);

				Message::message('useradmin', 'group_users_updated', array(':count' => $this->get_count_msg('user', $save_through_counts)), Message::$notice);
				$this->redirect_to_group_list();

			} catch (Exception $e) {
				Kohana_Exception::caught_handler($e);
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
			Kohana_Exception::caught_handler($e);
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