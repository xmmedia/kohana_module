<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Login extends Controller_Private {
	public $page = 'login';

	public $auth_required = FALSE;

	/**
	 * Stores the xm login config.
	 * Set in before().
	 *
	 * @var  array
	 */
	protected $login_config;

	/**
	 * Returns parent before and sets the login config property.
	 *
	 * @return  void
	 */
	public function before() {
		parent::before();

		$this->login_config = (array) Kohana::$config->load('xm_login');
	}

	/**
	* Displays the login form and logs the user in or detects and invalid login (through Auth and Model_User)
	*
	* View: Login form.
	*/
	public function action_index() {
		// set the template title (see Controller_App for implementation)
		$this->template->page_title = 'Login - ' . $this->page_title_append;

		// get some variables from the request
		// get the user name from a get parameter or a cookie (if set)
		$username = XM::get_param('username', Cookie::get('username'));
		$password = XM::get_param('password');
		$timed_out = XM::get_param('timed_out');
		// default to NULL when no redirect is received so it uses the default redirect
		$redirect = XM::get_param('redirect');

		// If user already signed-in
		if (Auth::instance()->logged_in()) {
			// redirect to the default login location or the redirect location
			$this->login_success_redirect($redirect);
		}

		// Get number of login attempts this session
		$attempts = Session::instance()->path($this->login_config['session_key'] . '.attempts', 0);
		$force_captcha = Session::instance()->path($this->login_config['session_key'] . '.force_captcha', FALSE);

		// If more than three login attempts, add a captcha to form
		$captcha_required = ($force_captcha || $attempts > $this->login_config['failed_login_captcha_display']);
		// Update number of login attempts
		++$attempts;
		Session::instance()->set_path($this->login_config['session_key'] . '.attempts', $attempts);

		// load recaptcha
		// do this here because there are likely to be a lot of accesses to this action that will never make it to here
		// loading it here will save server time finding (searching) and loading recaptcha
		Kohana::load(Kohana::find_file('vendor/recaptcha', 'recaptchalib'));

		try {
			// $_POST is not empty
			if ( ! empty($_POST)) {
				$human_verified = FALSE;
				$captcha_received = FALSE;

				// If recaptcha was set and is required
				if ($captcha_required && isset($_POST['recaptcha_challenge_field']) && isset($_POST['recaptcha_response_field'])) {
					$captcha_received = TRUE;
					// Test if recaptcha is valid
					$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
					$human_verified = $resp->is_valid;
					Message::add('ReCAPTCHA valid: ' . ($human_verified ? 'Yes' : 'No'), Message::$debug);
				} // if

				// if the captcha is required but we have not verified the human
				if ($captcha_required && ! $human_verified) {
					// increment the failed login count on the user
					$user = ORM::factory('User');
					$user->add_login_where($username)
						->find();

					// increment the login count and record the login attempt
					if ($user->loaded()) {
						$user->increment_failed_login();
					}

					$user->add_auth_log(Model_Auth_Log::LOG_TYPE_TOO_MANY_ATTEMPTS, $username);
					Message::message('user', 'recaptcha_not_valid');

				// Check Auth and log the user in if their username and password is valid
				} else if (($login_messages = Auth::instance()->login($username, $password, FALSE, $human_verified)) === TRUE) {
					$user = Auth::instance()->get_user();
					// user has to update their profile or password
					if ($user->force_update_profile_flag || $user->force_update_password_flag) {
						// add a message for the user regarding updating their profile or password
						$message_path = $user->force_update_profile_flag ? 'update_profile' : 'update_password';
						Message::message('user', $message_path, array(), Message::$notice);

						// instead of redirecting them to the location they requested, redirect them to the profile page
						$redirect = Route::get('account')->uri(array('action' => 'profile'));
					} // if

					if ( ! empty($redirect) && is_string($redirect)) {
						// loop through the routes till we find one that matches
						// it will return the array of params based on the URL
						$fake_request = Request::factory($redirect);
						foreach (Route::all() as $_route) {
							$found_params = $_route->matches($fake_request);
							if ($found_params !== FALSE) {
								break;
							}
						}
						if ( ! empty($found_params['controller']) && ! empty($found_params['action'])) {
							$next_controller_name = 'Controller_' . $found_params['controller'];
							if (class_exists($next_controller_name)) {
								$next_controller = new $next_controller_name($fake_request, Response::factory());
							}
						}

						// they have permission to access the page, so redirect them there
						if (isset($next_controller) && Auth::instance()->allowed($next_controller, $found_params['action'])) {
							$this->login_success_redirect($redirect);
						// they don't have permission to access the page, so just go to the default page
						} else {
							$this->login_success_redirect();
						}
					// redirect to the defualt location
					} else {
						$this->login_success_redirect();
					}

				// If login failed (captcha and/or wrong credentials)
				} else {
					// force captcha may have changed within Auth::login()
					$force_captcha = Session::instance()->path($this->login_config['session_key'] . '.force_captcha', FALSE);
					if ( ! $captcha_required && $force_captcha) {
						$captcha_required = TRUE;
					}

					if ( ! empty($login_messages)) {
						foreach ($login_messages as $message_data) {
							list($message, $values) = $message_data;
							Message::message('user', $message, $values, Message::$error);
						}
					}

					// determine if we should be displaying a recaptcha message
					if ( ! $human_verified && $captcha_received) {
						Message::message('user', 'recaptcha_not_valid', array(), Message::$error);
					} else if ($captcha_required && ! $captcha_received) {
						Message::message('user', 'enter_recaptcha', array(), Message::$error);
					}
				} // if
			} // if $_POST
		} catch (ORM_Validation_Exception $e) {
			Message::message('user', 'username.invalid');
		}

		if ( ! empty($timed_out)) {
			// they have come from the timeout page, so send them back there
			$this->redirect($this->current_route()->uri(array('action' => 'timedout')) . $this->get_redirect_query());
		}

		$this->template->body_html = View::factory('xm/login/login')
			->set('redirect', $redirect)
			->set('username', $username)
			->set('password', $password)
			->set('add_captcha', $captcha_required);
	} // function action_index

	/**
	* Redirects the user the first page they should see after login
	* $redirect contains the page they may have requested before logging in and they should be redirected there
	* If $redirect is is NULL then the default redirect from the config will be used
	*
	* @param  string  $redirect  The path to redirect to
	* @return  void  never returns
	*/
	protected function login_success_redirect($redirect = NULL) {
		if ($redirect !== NULL) {
			$this->redirect($redirect);
		} else {
			$auth_config = Kohana::$config->load('auth');
			$this->redirect(URL::site(Route::get($auth_config['default_login_redirect'])->uri($auth_config['default_login_redirect_params'])));
		}
	}

	/**
	* Log the user out and redirect to the login page.
	*/
	public function action_logout() {
		try {
			if (Auth::instance()->logout()) {
				Message::add(__(Kohana::message('user', 'username.logged_out')), Message::$notice);
			} // if
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
			Message::add(__(Kohana::message('user', 'username.not_logged_out')), Message::$error);

			// redirect them to the default page
			$this->login_success_redirect();
		} // try

		// redirect to the user account and then the signin page if logout worked as expected
		$this->redirect($this->current_route()->uri() . $this->get_redirect_query());
	}

	/**
	* Display a page that displays the username and asks the user to enter the password
	* This is for when their session has timed out, but we don't want to make the login fully again
	* If the user has fully timed out, they will be logged out and returned to the login page
	*/
	public function action_timedout() {
		$user = Auth::instance()->get_user();

		$max_lifetime = Kohana::$config->load('auth.timed_out_max_lifetime');

		if ( ! $user || ($max_lifetime > 0 && Auth::instance()->timed_out($max_lifetime))) {
			// user is not logged in at all or they have reached the maximum amount of time we allow sometime to stay logged in, so redirect them to the login page
			$this->redirect($this->current_route()->uri(array('action' => 'logout')) . $this->get_redirect_query());
		}

		$timeout_post = Session::instance()->get(Kohana::$config->load('xm_login.timeout_post_session_key'));
		if (Kohana::$config->load('xm_login.enable_timeout_post') && ! empty($timeout_post)) {
			$redirect = Route::get('login')->uri(array('action' => 'timeoutpost'));
		} else {
			// need to decode the redirect as it will be encoded in the URL
			$redirect = XM::get_param('redirect');
		}

		$this->template->page_title = 'Timed Out - ' . $this->page_title_append;
		$this->template->body_html = View::factory('xm/login/timed_out')
			->set('redirect', $redirect)
			->set('username', $user->username);

		$this->add_on_load_js('$(\'#password\').focus();');
	} // function action_timedout

	/**
	* Creates a form with all the fields from the GET and POST and then submits the form
	* to the page they were originally submitted to.
	*
	* @return  void
	*
	* @uses  Form::array_to_fields()
	*/
	public function action_timeoutpost() {
		// we want to redirect the user to the previous form, first creating the form and then submitting it with JS
		$timeout_post_session_key = Kohana::$config->load('xm_login.timeout_post_session_key');

		$timeout_post = Session::instance()->get($timeout_post_session_key);
		if ( ! Kohana::$config->load('xm_login.enable_timeout_post') || empty($timeout_post)) {
			$this->login_success_redirect();
		}

		try {
			$form_html = Form::open(URL::site($timeout_post['post_to']), array('id' => 'timeout_post')) . EOL;
			if ( ! empty($timeout_post['get'])) {
				$form_html .= Form::array_to_fields($timeout_post['get']);
			}
			if ( ! empty($timeout_post['post'])) {
				$form_html .= Form::array_to_fields($timeout_post['post']);
			}
			$form_html .= Form::close();

			$this->template->body_html = $form_html;
			$this->add_on_load_js('$(\'#timeout_post\').submit();');

			Session::instance()->delete($timeout_post_session_key);
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
			$this->login_success_redirect();
		}
	} // function action_timeoutpost

	/**
	* View: Access not allowed.
	*/
	public function action_noaccess() {
		// set the template title (see Controller_App for implementation)
		$this->template->page_title = 'Access Not Allowed - ' . $this->page_title_append;
		$view = $this->template->body_html = View::factory('xm/login/no_access')
			->set('referrer', XM::get_param('referrer'));
	}

	/**
	* A basic implementation of the "Forgot password" functionality
	*/
	public function action_forgot() {
		// If user already signed-in to redirect them to the default page
		if (Auth::instance()->logged_in()) {
			$this->login_success_redirect();
		}

		$reset_username = UTF8::trim($this->request->post('reset_username'));
		$forgot_submitted = (bool) $this->request->post('forgot_submitted');

		if ( ! empty($reset_username)) {
			$user = ORM::factory('User')
				->where('username', 'LIKE', $reset_username)
				->where_active('user')
				->find();

			// Admin passwords cannot be reset by email
			if ($user->loaded() && ! in_array($user->username, $this->login_config['admin_accounts'])) {
				$reset = ORM::factory('User_Reset')
					->values(array(
						'user_id' => $user->pk(),
						'token' => Text::random('alnum', 32),
						'browser' => ( ! empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''),
						'ip_address' => ( ! empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ''),
					))
					->save();

				$mail = new Mail();
				$mail->IsHTML();
				$mail->AddUser($user->pk());
				$mail->Subject = LONG_NAME . ' Password Reset';

				// build a link with action reset including their username and the reset token
				$url = URL::site($this->current_route()->uri(array('action' => 'reset')) . '?' . http_build_query(array(
					'token' => $reset->token,
				)), FALSE);

				$mail->Body = View::factory('xm/login/forgot_link_email')
					->set('app_name', LONG_NAME)
					->set('url', $url)
					->set('admin_email', ADMIN_EMAIL);

				$mail->Send();

				Message::add(__(Kohana::message('login', 'reset_link_sent')), Message::$notice);
				$this->redirect($this->current_route()->uri());

			} else if (in_array($user->username, $this->login_config['admin_accounts'])) {
				Message::add(__(Kohana::message('login', 'reset_admin_account')), Message::$warning);

			} else {
				Message::add(__(Kohana::message('login', 'reset_not_found')), Message::$warning);
			}
		} else if ($forgot_submitted) {
			Message::add(__(Kohana::message('login', 'reset_email_empty')), Message::$error);
		} // if post

		$this->template->page_title = 'Reset Your Password - ' . $this->page_title_append;
		$this->template->body_html = View::factory('xm/login/forgot');
	} // function action_forgot

	/**
	 * Displays and processes the change password form for users that are not logged in,
	 * coming from a forgot/reset password email.
	 *
	 * @return  void
	 */
	public function action_reset() {
		// If user already signed-in to redirect them to the default page
		if (Auth::instance()->logged_in()) {
			$this->login_success_redirect();
		}

		$token = UTF8::trim($this->request->query('token'));
		if (empty($token)) {
			$token = UTF8::trim($this->request->post('token'));
		}

		// make sure that the token has exactly 32 characters
		if ( ! empty($token) && UTF8::strlen($token) == 32) {
			$reset = ORM::factory('User_Reset')
				->where('token', '=', $token)
				->where('datetime', '>=', Date::formatted_time('-' . $this->login_config['reset_valid_time']))
				->find();

			if ($reset->loaded()) {
				$user = $reset->user;

				// make sure we found the user & their account is active
				// admin passwords cannot be reset by email
				if ($user->loaded() && $user->active_flag && ! in_array($user->username, $this->login_config['admin_accounts'])) {
					$new_password_submitted = (bool) $this->request->post('new_password_submitted');

					if ($new_password_submitted) {
						$password = $this->request->post('password');
						$password_confirm = $this->request->post('password_confirm');
						$password_min_length = (int) Kohana::$config->load('auth.password_min_length');

						if (empty($password) || UTF8::strlen($password) < $password_min_length) {
							Message::add(__(Kohana::message('login', 'password_min_length')), Message::$error);
						} else if ($password != $password_confirm) {
							Message::add(__(Kohana::message('login', 'passwords_different')), Message::$error);
						} else {
							$user->values(array(
									'password' => $password,
									// reset the failed login count
									'failed_login_count' => 0,
									// don't force the user to update their password on login since they've just updated their password
									'force_update_password_flag' => 0,
								))
								->is_valid()
								->save();

							foreach($user->user_reset->find_all() as $_reset) {
								$_reset->delete();
							}

							// send an email to the user notifying them of the password change
							$uri = $this->current_route()->uri();
							$user->send_password_changed_email($uri);

							Session::instance()->set_path($this->login_config['session_key'] . '.force_captcha', FALSE);
							Session::instance()->set_path($this->login_config['session_key'] . '.attempts', 0);

							Message::add(__(Kohana::message('login', 'password_saved')), Message::$notice);
							$this->redirect($this->current_route()->uri());
						}
					}

					$this->template->page_title = 'Enter a New Password - ' . $this->page_title_append;
					$this->template->body_html = View::factory('xm/login/forgot_reset')
						->set('token', $token);

				} else {
					Message::add(__(Kohana::message('login', 'token_not_found')), Message::$error);
					$this->redirect($this->current_route()->uri(array('action' => 'forgot')));
				}
			} else {
				Message::add(__(Kohana::message('login', 'token_not_found')), Message::$error);
				$this->redirect($this->current_route()->uri(array('action' => 'forgot')));
			}

		} else {
			Message::add(__(Kohana::message('login', 'password_email_partial')), Message::$error);
			$this->redirect($this->current_route()->uri(array('action' => 'forgot')));
		}
	} // function action_reset

	/**
	* Returns the redirect value as a query string ready to use in a direct
	* The ? is added at the beginning of the string
	* An empty string is returned if there is no redirect parameter
	*
	* @return	string
	*/
	protected function get_redirect_query() {
		$redirect = urldecode(XM::get_param('redirect'));

		if ( ! empty($redirect)) {
			return URL::array_to_query(array('redirect' => $redirect), '&');
		} else {
			return '';
		}
	}
}