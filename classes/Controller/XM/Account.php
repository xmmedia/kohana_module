<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Account controller for user to change their accounts settings (ie, username, name and password).
 *
 * @package    XM
 * @category   Controllers
 * @author     XM Media Inc.
 * @copyright  (c) 2014 XM Media Inc.
 */
class Controller_XM_Account extends Controller_Private {
	public $page = 'account';

	/**
	* The profile and password actions require the account/profile permission.
	* @var  array
	* @see  Controller_Base
	*/
	public $secure_actions = array(
		'profile' => 'account/profile',
		'password' => 'account/profile',
	);

	protected $default_uri;

	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->add_style('account', 'xm/css/account.css');
		}

		$this->default_uri = $this->current_route()->uri(array('action' => 'profile'));
	}

	/**
	 * Redirects the user to the profile page.
	 *
	 * @return  void
	 */
	public function action_index() {
		$this->redirect($this->default_uri);
	}

	/**
	 * Displays and processes the profile form.
	 *
	 * @return  void
	 */
	public function action_profile() {
		// use the user loaded from auth to get the user profile model (extends user)
		$user = ORM::factory('User', Auth::instance()->get_user()->pk())
			->set_for_profile_edit()
			->set_mode('edit');
		if ( ! $user->loaded()) {
			throw new Kohana_Exception('The user could not be retrieved');
		}

		if ( ! empty($_POST)) {
			try {
				// store the post values
				$user->save_values()
				// the user no longer is forced to update their profile
					->set('force_update_profile_flag', FALSE)
				// save first, so that the model has an id when the relationships are added
					->save();

				// reload the user in the session
				Auth::instance()->get_user()->reload();

				Message::message('account', 'profile_saved', NULL, Message::$notice);
				// redirect because they have changed their name, which is displayed on the page
				$this->redirect($this->default_uri);

			} catch (ORM_Validation_Exception $e) {
				Message::message('account', 'profile_save_validation', array(
					':validation_errors' => Message::add_validation_errors($user->validation(), 'user')
				), Message::$error);
			}
		}

		$columns = array();
		foreach ($user->table_columns() as $column_name => $column_meta_data) {
			if ($user->show_field($column_name)) {
				$columns[] = $column_name;
			}
		}

		$form_open = Form::open($this->default_uri, array('method' => 'post'));
		$password_uri = $this->current_route()->uri(array('action' => 'password'));

		$this->template->page_title = 'Profile Edit - ' . $this->page_title_append;
		$this->template->body_html = View::factory('xm/account/profile')
			->bind('user', $user)
			->bind('form_open', $form_open)
			->bind('columns', $columns)
			->bind('default_uri', $this->default_uri)
			->bind('password_uri', $password_uri);
	}

	/**
	 * Displays and processes the change password form.
	 *
	 * @return  void
	 */
	public function action_password() {
		if ( ! empty($_POST)) {
			$user = ORM::factory('User', Auth::instance()->get_user()->pk());
			if ( ! $user->loaded()) {
				throw new Kohana_Exception('The user could not be retrieved');
			}
			$user_rules = $user->rules();

			$validation = Validation::factory($this->request->post())
				->labels(array(
					'current_password' => 'Current Password',
					'new_password' => 'New Password',
					'new_password_confirm' => 'Confirm New Password',
				))
				->rules('current_password', $user_rules['password'])
				->rules('new_password', $user_rules['password'])
				->rules('new_password_confirm', array(array('matches', array(':validation', 'new_password', 'new_password_confirm'))));
			$validation->check();
			$errors = $validation->errors();

			// if there are no errors above, check to see if the current password matches the one in DB
			if (empty($errors)) {
				if (Kohana::$config->load('auth.enable_3.0.x_hashing')) {
					if (Auth::instance()->hash_password((string) $validation['current_password'], Auth::instance()->find_salt($user->password)) !== $user->password) {
						$validation->error('current_password', 'not_the_same');
					}
				} else {
					if (Auth::instance()->hash((string) $validation['current_password']) !== $user->password) {
						$validation->error('current_password', 'not_the_same');
					}
				}

				// repopulate the error array
				$errors = $validation->errors();
			}

			// now save the user
			if (empty($errors)) {
				$user->values(array(
						'password' => $this->request->post('new_password'),
						// user no longer needs to update their password
						'force_update_password_flag' => FALSE,
					))
					->save();

				// reload the user in the session
				Auth::instance()->get_user()->reload();

				// send an email to the user notifying them of the password change
				$uri = $this->current_route()->uri();
				$user->send_password_changed_email($uri);

				Message::message('account', 'password_changed', NULL, Message::$notice);
				// redirect and exit
				$this->redirect($this->default_uri);

			} else {
				$msg = __(Kohana::message('account', 'password_change_validation'));
				$msg .= Message::add_validation_errors($validation, 'account');
				Message::add($msg, Message::$error);
			}
		}

		$form_open = Form::open($this->current_route()->uri(array('action' => 'password')), array('method' => 'post'));

		$this->template->page_title = 'Change Password - ' . $this->page_title_append;
		$this->template->body_html = View::factory('xm/account/password')
			->bind('form_open', $form_open)
			->bind('default_uri', $this->default_uri);
	}
} // class