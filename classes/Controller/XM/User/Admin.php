<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_User_Admin extends Controller_Private {
	public $auth_required = TRUE;
	public $secure_actions = array(
		'index' => 'user_admin/index',
		'add' => 'user_admin/add',
		'edit' => 'user_admin/edit',
		'delete' => 'user_admin/delete',
		'view' => 'user_admin/index',
		'email_password' => 'user_admin/email_password',
		'cancel' => 'user_admin/index',
		'groups' => 'user_admin/group/index',
		'add_group' => 'user_admin/group/add',
		'edit_group' => 'user_admin/group/edit',
		'delete_group' => 'user_admin/group/delete',
		'view_group' => 'user_admin/group/index',
		'group_permissions' => 'user_admin/group/permissions',
		'group_users' => 'user_admin/group/users',
		'cancel_group' => 'user_admin/group/index',
	);
	public $page = 'user_admin';

	protected $id;
	protected $page_offset;
	protected $sort_column;
	protected $sort_direction;
	protected $search;
	protected $search_applied = FALSE;
	protected $user_admin_session;

	protected $list_headings = array(
		'',
		'user_admin.active_flag' => 'Active',
		'user_admin.username' => 'Email (Username)',
		'user_admin.last_name,user_admin.first_name' => 'Name',
		'Permission Groups',
		'user_admin.login_count' => 'Login Count',
		'user_admin.last_login' => 'Last Login',
	);
	protected $group_list_headings = array(
		'',
		'Name',
		'Description',
	);
	protected $page_max_rows = 30;

	protected $default_search = array('text' => NULL, 'group_id' => NULL, 'only_active' => 1);

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
		$search = Arr::get($_REQUEST, 'search');

		$this->user_admin_session = Session::instance()->get('user_admin');
		if (empty($this->user_admin_session)) {
			$this->user_admin_session = array(
				'users' => array(
					'page_offset' => 0,
					'sort_column' => NULL,
					'sort_direction' => NULL,
					'search' => $this->default_search,
				),
				'groups' => array(
					'page_offset' => 0,
				),
			);
		}

		$this->add_css()
			->add_js();

		if ($this->request->action() == 'groups') {
			$page_title = 'Groups';

			if ($page_offset !== NULL) $this->user_admin_session['groups']['page_offset'] = intval($page_offset);
			$this->page_offset = $this->user_admin_session['groups']['page_offset'];

		} else if ($this->request->action() == 'index') {
			$page_title = 'Users';

			if ($page_offset !== NULL) $this->user_admin_session['users']['page_offset'] = intval($page_offset);
			if ($search !== NULL) {
				if ( ! isset($search['only_active'])) {
					$search['only_active'] = FALSE;
				} else {
					$search['only_active'] = (bool) $search['only_active'];
				}
				$this->user_admin_session['users']['search'] = (array) $search + $this->default_search;
			}
			$this->page_offset = $this->user_admin_session['users']['page_offset'];
			$this->search = $this->user_admin_session['users']['search'];

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
			$this->template->pre_message = View::factory('user_admin/page_title')
				->set('page_title', $page_title);
		}
	} // function before

	public function after() {
		$this->set_session();

		parent::after();
	}

	protected function add_css() {
		if ($this->auto_render) {
			$this->add_style('dbadmin', 'css/dbadmin.css')
				->add_style('user_admin', 'xm/css/user_admin.css');
		}

		return $this;
	} // function add_admin_css

	public function add_js() {
		if ($this->auto_render) {
			$this->add_script('user_admin', 'xm/js/user_admin.js');
		}

		return $this;
	}

	/**
	 * Sets the values from the controller property in the session.
	 * Otherwise we'll loose the values.
	 *
	 * @return void
	 */
	protected function set_session() {
		Session::instance()->set('user_admin', $this->user_admin_session);
	}

	public function action_index() {
		$offset = $this->page_offset;
		if ($offset > 0) {
			// subtract 1 because the first page_offset really by 0, but is passed as 1
			--$offset;
		}

		$user = $this->get_user_orm_list($this->page_max_rows, $offset);

		$users = $user->find_all();

		$this->add_user_search($user);
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
				'class' => 'xm_content user_admin_list',
			),
			'heading' => array(),
		);

		$sort_url = $this->request->route()->uri();
		$i = 0;
		foreach ($this->list_headings as $sort_column => $heading) {
			// if there is no sort column, then this column be used in sorting (ie, permission groups)
			if (empty($sort_column) || is_int($sort_column)) {
				$table_options['heading'][] = HTML::chars($heading);
			} else {
				if ($sort_column != $this->sort_column) {
					$sort_query = 'sort_column=' . urlencode($sort_column) . '&sort_direction=ASC';
				// the current column is the column being sorted
				} else {
					if ($this->sort_direction == 'ASC') {
						$sort_query = 'sort_column=' . urlencode($sort_column) . '&sort_direction=DESC';
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

		$this->template->page_title = 'User Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/user_list')
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
		$users = ORM::factory('User_Admin')
			->set_options(array('mode' => 'view'))
			->limit($page_max_rows)
			->offset($offset * $page_max_rows);

		$this->add_user_search($users);

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
	 * Adds the search criteria to the query.
	 * Used for both generating the list of users and also the count.
	 *
	 * @param  Model_User  $users  The user model (or a query) to apply the where clauses to.
	 * @return void
	 */
	protected function add_user_search($users) {
		if ( ! empty($this->search['text'])) {
			$text_search = '%' . $this->search['text'] . '%';
			$users->where_open()
					->or_where('user_admin.first_name', 'LIKE', $text_search)
					->or_where('user_admin.last_name', 'LIKE', $text_search)
					->or_where('user_admin.username', 'LIKE', $text_search)
				->where_close();

			$this->search_applied = TRUE;
		}

		if ( ! empty($this->search['group_id']) && $this->search['group_id'] != 'all') {
			$users->join(array('user_group', 'ug'), 'INNER')
					->on('user_admin.id', '=', 'ug.user_id')
				->where('ug.group_id', '=', $this->search['group_id']);

			$this->search_applied = TRUE;
		}

		if ($this->search['only_active']) {
			$users->where('user_admin.active_flag', '=', 1);
		} else {
			$this->search_applied = TRUE;
		}
	} // function add_user_search

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

		$first_col = HTML::anchor($this->request->route()->uri(array('action' => 'view', 'id' => $id)), HTML::icon('view'), array(
			'title' => __('View this user'),
		));

		if (Auth::instance()->allowed('user_admin/edit')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'edit', 'id' => $id)), HTML::icon('edit'), array(
				'title' => __('Edit this user'),
			));
		}

		if (Auth::instance()->allowed('user_admin/delete')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'delete', 'id' => $id)), HTML::icon('delete'), array(
				'title' => __('Delete this user'),
			));
		}

		if (Auth::instance()->allowed('user_admin/add')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'add', 'id' => $id)), HTML::icon('add'), array(
				'title' => __('Copy this user'),
			));
		}

		if (Auth::instance()->allowed('user_admin/email_password')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'email_password', 'id' => $id)), HTML::icon('mail'), array(
				'title' => __('Email a new random password to this user'),
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
			Form::submit(NULL, 'Add New User', array('class' => 'js_xm_button_link xm_list_button', 'data-xm_link' => URL::site($this->request->route()->uri(array('action' => 'add'))))),
		);
		if ( ! empty($this->sort_column) || $this->search_applied) {
			$list_buttons[] = Form::input_button(NULL, 'Clear Search/Sort', array('class' => 'js_xm_button_link xm_list_button', 'data-xm_link' => URL::site($this->request->route()->uri() . '?sort_column=&sort_direction=&search%5Btext%5D=&search%5Bgroup_id%5D&search%5Bonly_active%5D=1')));
		}

		$list_buttons[] = Form::open(NULL, array('method' => 'GET'))
			. Form::input('search[text]', $this->search['text'])
			. Form::select('search[group_id]', $this->allowed_groups(), $this->search['group_id'], array(), array('add_values' => array('all' => '-- All Permission Groups --')))
			. Form::checkbox('search[only_active]', 1, (bool) $this->search['only_active'], array('id' => 'user_search_only_active')) . Form::label('user_search_only_active', 'Only Active Users')
			. Form::submit(NULL, 'Search')
			. Form::close();

		return $list_buttons;
	} // function get_user_list_buttons

	public function action_view() {
		if ( ! ($this->id > 0)) {
			throw new Kohana_Exception('No ID received for view');
		}

		$user = ORM::factory('User_Admin', $this->id)
			->set_mode('view')
			->set_option('get_view_view_file', 'user_admin/user_view_form');

		$this->template->page_title = $user->first_name . ' ' . $user->last_name . ' - View User - User Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/user_view')
			->bind('user', $user);
	} // function action_view

	/**
	* Display an add form or add (save) a new record
	*/
	public function action_add() {
		$user = ORM::factory('User_Admin', $this->id)
			->set_mode('add')
			->set_option('get_form_view_file', 'user_admin/user_edit_form');

		$this->set_user_permission_edit($user);

		if ( ! empty($_POST)) {
			$this->save_user($user);
		}

		// don't show the failed login count on add as it should default to 0
		$user->set_table_columns('failed_login_count', 'edit_flag', FALSE);

		if ( ! empty($this->id)) {
			$user->set_option('form_action', URL::site(Request::current()->route()->uri(array('action' => 'add'))) . URL::query());
		}

		$this->template->page_title = 'Add User - User Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/user_edit')
			->bind('user', $user);
	} // function action_add

	/**
	* Display an edit form for a record or update (save) an existing record
	*/
	public function action_edit() {
		$user = ORM::factory('User_Admin', $this->id)
			->set_mode('edit')
			->set_option('get_form_view_file', 'user_admin/user_edit_form');

		// if the user can edit permissions groups, but doesn't have access to all groups
		// look for groups that they don't have access to but are on this user and add a message
		if ($this->set_user_permission_edit($user) && ! Auth::instance()->allowed('user_admin/user/group/*')) {
			$user_groups = $user->group->find_all()->as_array('id', 'name');
			$other_groups = '';
			$other_count = 0;
			foreach ($user_groups as $group_id => $group_name) {
				if ( ! Auth::instance()->allowed('user_admin/user/group/' . $group_id)) {
					if ($other_count > 0) $other_groups .= ', ';
					$other_groups .= $group_name;
					++$other_count;
				}
			}
			if ($other_count > 0) {
				Message::message('user_admin', 'other_groups', array(':other_groups' => HTML::chars($other_groups)), Message::$notice);
			}
		} // if

		if ( ! empty($_POST)) {
			$this->save_user($user);
		} // if

		$this->template->page_title = $user->first_name . ' ' . $user->last_name . ' - Edit User - User Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/user_edit')
			->bind('user', $user);
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
		if ( ! Auth::instance()->allowed('user_admin/edit/permissions')) {
			$user->set_has_many('group', 'edit_flag', FALSE);
			return FALSE;

		// if they don't have permission to add all groups, then check for the groups they do have permissions to
		} else if ( ! Auth::instance()->allowed('user_admin/user/group/*')) {
			$allowed_groups = $this->allowed_groups();
			if ( ! empty($allowed_groups)) {
				$user->set_has_many('group', 'source.source', 'array')
					->set_has_many('group', 'source.data', $allowed_groups);
				return TRUE;
			} else {
				$user->set_has_many('group', 'edit_flag', FALSE);
				return FALSE;
			}
		} else if (Auth::instance()->allowed('user_admin/user/group/*')) {
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
			$allowed_all_groups = Auth::instance()->allowed('user_admin/user/group/*');
			$this->allowed_groups = array();
			foreach (ORM::factory('Group')->find_all() as $group) {
				if ($allowed_all_groups || Auth::instance()->allowed('user_admin/user/group/' . $group->pk())) {
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
			if ($this->set_user_permission_edit($user) && ! Auth::instance()->allowed('user_admin/user/group/*')) {
				// look in the post for any of the groups that the user is not allowed to add users to (security check)
				$allowed_groups = $this->allowed_groups();
				$selected_groups = Arr::path($post, 'c_record.group', array());
				foreach ($selected_groups as $key => $group_id) {
					if ( ! isset($allowed_groups[$group_id])) {
						unset($selected_groups[$key]);
					}
				}

				// re-add any groups that the user doesn't have permissions to
				$other_groups = ORM::factory('User_Group')
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
			$user->save_values($post);

			// false if this is not a new user
			$new_user = ! $user->loaded();
			// if it's a new user and they didn't enter a password, generate a password
			if ($new_user && empty($user->password)) {
				$new_password = Text::random('distinct');
				$user->values(array(
					'password' => $new_password,
					'password_confirm' => $new_password,
				));
			}

			$user->save();

			$this->user_additional_save($user);

			$send_email = XM::get_param('send_email', FALSE);
			if ($send_email) {
				if ( ! isset($new_password)) {
					$new_password = XM::get_param_array(array('c_record', 'user', 0, 'password'), FALSE);
					$new_password = (empty($new_password) ? FALSE : $new_password);
				}

				$mail = new Mail();
				$mail->IsHTML();
				$mail->AddUser($user->id);
				$mail->Subject = SHORT_NAME . ' Login Information';
				$editing_user = Auth::instance()->get_user();
				if (Valid::email($editing_user->username)) {
					$mail->AddReplyTo($editing_user->username, $editing_user->first_name . ' ' . $editing_user->last_name);
				}

				// provide a link to the user including their username
				$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

				$mail->Body = View::factory('user_admin/' . ($new_user ? 'new_account_email' : 'account_update_email'))
					->set('app_name', LONG_NAME)
					->set('username', $user->username)
					->set('password', $new_password)
					->set('url', $url)
					->set('support_email', Kohana::$config->load('user_admin.support_email'));

				$mail->Send();

				Message::message('user_admin', 'email_account_info', array(), Message::$notice);
			}

			Message::message('user_admin', 'user_saved', NULL, Message::$notice);
			$this->redirect_to_index();

		} catch (ORM_Validation_Exception $e) {
			Message::message('xm_db_admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, '')
			), Message::$error);
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
		if ( ! ($this->id > 0)) {
			Message::message('xm_db_admin', 'no_id', NULL, Message::$error);
			$this->redirect_to_index();
		} // if

		if ( ! empty($_POST)) {
			// see if they want to delete the item
			if (strtolower($_POST['xm_delete_confirm']) == 'yes') {
				$user = ORM::factory('User_Admin', $this->id);
				if ($user->delete() == 0) {
					Message::message('xm_db_admin', 'no_item_deleted', NULL, Message::$error);
				} else {
					Message::message('user_admin', 'user_deleted', array(), Message::$notice);
					Message::message('xm_db_admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
				} // if
			} else {
				Message::message('xm_db_admin', 'item_not_deleted', NULL, Message::$notice);
			}

			$this->redirect_to_index();

		} else {
			// the confirmation form goes in the messages
			Message::add(View::factory('user_admin/confirm_delete'));

			$this->action_view();
		}
	} // function action_delete

	public function action_email_password() {
		if ( ! ($this->id > 0)) {
			Message::message('xm_db_admin', 'no_id', NULL, Message::$error);
			$this->redirect_to_index();
		} // if

		$new_password = XM_Auth::generate_password();

		$user = ORM::factory('User', $this->id)
			->values(array(
				'password' => $new_password,
				'force_update_password_flag' => 1,
				'failed_login_count' => 0,
				'last_failed_login' => 0,
			))
			->save();

		$mail = new Mail();
		$mail->IsHTML();
		$mail->AddUser($user->id);
		$mail->Subject = SHORT_NAME . ' Login Information';
		$editing_user = Auth::instance()->get_user();
		if (Valid::email($editing_user->username)) {
			$mail->AddReplyTo($editing_user->username, $editing_user->first_name . ' ' . $editing_user->last_name);
		}

		// provide a link to the user including their username
		$url = URL::site(Route::get('login')->uri(), TRUE) . '?' . http_build_query(array('username' => $user->username));

		$mail->Body = View::factory('user_admin/login_information_email')
			->set('app_name', LONG_NAME)
			->set('username', $user->username)
			->set('password', $new_password)
			->set('url', $url)
			->set('support_email', Kohana::$config->load('user_admin.support_email'));

		$mail->Send();

		Message::message('user_admin', 'email_password_sent', array(), Message::$notice);

		$this->redirect_to_index();
	} // function action_email_password

	/**
	* Cancel the current action by redirecting back to the index action
	*/
	public function action_cancel() {
		// add a notice to be displayed
		Message::message('xm_db_admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_index();
	}

	public function redirect_to_index() {
		$this->set_session();
		$this->redirect(Route::get(Route::name(Request::current()->route()))->uri());
	}

	public function action_groups() {
		$group = ORM::factory('Group')
			->set_options(array('mode' => 'view'));
		$groups = $group->find_all();
		$group_count = $group->count_all();

		$table_options = array(
			'table_attributes' => array(
				'class' => 'xm_content user_admin_group_list',
			),
			'heading' => $this->group_list_headings,
		);

		$table = new HTMLTable($table_options);

		foreach ($groups as $group) {
			$group->set_mode('view');
			$table->add_row($this->get_group_list_row($group));
		} // foreach

		$this->template->page_title = 'Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_list')
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

		$first_col = HTML::anchor($this->request->route()->uri(array('action' => 'view_group', 'id' => $id)), HTML::icon('view'), array(
			'title' => __('View this user'),
		));

		if (Auth::instance()->allowed('user_admin/group/edit')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'edit_group', 'id' => $id)), HTML::icon('edit'), array(
				'title' => __('Edit this group'),
			));
		}

		if (Auth::instance()->allowed('user_admin/group/delete')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'delete_group', 'id' => $id)), HTML::icon('delete'), array(
				'title' => __('Delete this group'),
			));
		}

		if (Auth::instance()->allowed('user_admin/group/add')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'add_group', 'id' => $id)), HTML::icon('add'), array(
				'title' => __('Copy this group'),
			));
		}

		if (Auth::instance()->allowed('user_admin/group/permissions')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'group_permissions', 'id' => $id)), HTML::icon('lock'), array(
				'title' => __('Edit the permissions for this group'),
			));
		}

		if (Auth::instance()->allowed('user_admin/group/users')) {
			$first_col .= HTML::anchor($this->request->route()->uri(array('action' => 'group_users', 'id' => $id)), HTML::icon('contact2'), array(
				'title' => __('Edit the users that have this permission group'),
			));
		}

		return $first_col;
	} // function get_group_list_row_links

	public function action_view_group() {
		if ( ! ($this->id > 0)) {
			throw new Kohana_Exception('No ID received for view');
		}

		$group = ORM::factory('Group', $this->id)
			->set_mode('view')
			->set_option('get_view_view_file', 'user_admin/group_view_form')
			->set_option('display_buttons', FALSE);

		$this->template->page_title = $group->name . ' - View Group - Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_view')
			->bind('group', $group);
	} // function action_view_group

	/**
	* Display an add form or add (save) a new record
	*/
	public function action_add_group() {
		$group = ORM::factory('Group', $this->id)
			->set_mode('add')
			->set_option('cancel_button_attributes', array(
				'data-xm_link' => URL::site(Route::get('user_admin')->uri(array('action' => 'cancel_group'))),
			));

		if ( ! empty($_POST)) {
			$this->save_group($group);
		}

		if ( ! empty($this->id)) {
			$group->set_option('form_action', URL::site($this->request->route()->uri(array('action' => $this->request->action()))) . URL::query());
		}

		$this->template->page_title = 'Add Group - Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_edit')
			->bind('group', $group);
	} // function action_add

	/**
	* Display an edit form for a record or update (save) an existing record
	*/
	public function action_edit_group() {
		$group = ORM::factory('Group', $this->id)
			->set_mode('edit')
			->set_option('cancel_button_attributes', array(
				'data-xm_link' => URL::site(Route::get('user_admin')->uri(array('action' => 'cancel_group'))),
			));

		if ( ! empty($_POST)) {
			$this->save_group($group);
		} // if

		$this->template->page_title = $group->name . ' - Edit Group - Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_edit')
			->bind('group', $group);
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

			Message::message('xm_db_admin', 'item_saved', NULL, Message::$notice);
			$this->redirect_to_group_list();

		} catch (ORM_Validation_Exception $e) {
			Message::message('xm_db_admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, '')
			), Message::$error);
		}
	} // function save_user

	/**
	* Delete a record with a confirm first
	*/
	public function action_delete_group() {
		if ( ! ($this->id > 0)) {
			Message::message('xm_db_admin', 'no_id', NULL, Message::$error);
			$this->redirect_to_group_list();
		} // if

		if ( ! empty($_POST)) {
			// see if they want to delete the item
			if (strtolower($_POST['xm_delete_confirm']) == 'yes') {
				$group = ORM::factory('Group', $this->id);
				if ($group->delete() == 0) {
					Message::message('xm_db_admin', 'no_item_deleted', NULL, Message::$error);
				} else {
					Message::message('user_admin', 'user_deleted', array(), Message::$notice);
					Message::message('xm_db_admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
				} // if
			} else {
				Message::message('xm_db_admin', 'item_not_deleted', NULL, Message::$notice);
			}

			$this->redirect_to_group_list();

		} else {
			// the confirmation form goes in the messages
			Message::add(View::factory('user_admin/confirm_delete'));

			$this->action_view_group();
		}
	} // function action_delete

	/**
	* Cancel the current action by redirecting back to the groups action
	*/
	public function action_cancel_group() {
		// add a notice to be displayed
		Message::message('xm_db_admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_group_list();
	} // function

	public function redirect_to_group_list() {
		$this->set_session();
		$this->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'groups')));
	}

	public function action_group_permissions() {
		if ( ! empty($_POST)) {
			ORM::factory('Group', $this->id)
				->save_through('permission', 'current_permissions', $save_through_counts);

			Message::message('user_admin', 'group_permissions_updated', array(':count' => $this->get_count_msg('permission', $save_through_counts)), Message::$notice);
			$this->redirect_to_group_list();
		} // if

		$group = ORM::factory('Group', $this->id);

		$select_perm_id = 'permission.id';
		$select_perm_name = array(DB::expr("CONCAT_WS('', permission.name, ' (', permission.permission, ')')"), 'permission_name');

		$all_permissions = ORM::factory('Permission')
			->select($select_perm_id)
			->select($select_perm_name)
			->find_all()
			->as_array('id', 'permission_name');

		$current_permissions = ORM::factory('Group', $this->id)
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
			$permission_groups = ORM::factory('Permission_Group')
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

		$this->template->page_title = $group->name . ' - Edit Permissions - Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_permission_edit')
			->bind('group', $group)
			->bind('available_perms_select', $available_perms_select)
			->bind('current_perms_select', $current_perms_select)
			->bind('permission_group_select_add', $perm_group_select_add)
			->bind('permission_group_select_remove', $perm_group_select_remove);
	} // function action_group_permissions

	public function action_group_users() {
		if ( ! empty($_POST)) {
			ORM::factory('Group', $this->id)
				->save_through('user', 'current_users', $save_through_counts);

			Message::message('user_admin', 'group_users_updated', array(':count' => $this->get_count_msg('user', $save_through_counts)), Message::$notice);
			$this->redirect_to_group_list();
		} // if

		$group = ORM::factory('Group', $this->id);

		$select_user_id = 'user.id';
		$select_user_name = array(DB::expr("CONCAT_WS('', user.first_name, ' ', user.last_name)"), 'name');

		$all_users = ORM::factory('User')
			->select($select_user_id)
			->select($select_user_name)
			->find_all()
			->as_array('id', 'name');

		$current_users = ORM::factory('Group', $this->id)
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

		$this->template->page_title = $group->name . ' - Edit Users - Permission Groups - ' . $this->page_title_append;
		$this->template->body_html = View::factory('user_admin/group_user_edit')
			->bind('group', $group)
			->bind('available_users_select', $available_users_select)
			->bind('current_users_select', $current_users_select);
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
} // class Controller_User_Admin