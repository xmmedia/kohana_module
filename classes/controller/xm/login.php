<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_Login extends Controller_cl4_Login {
	/**
	* A basic implementation of the "Forgot password" functionality
	*/
	public function action_forgot() {
		try {
			Kohana::load(Kohana::find_file('vendor/recaptcha', 'recaptchalib'));

			$default_options = Kohana::$config->load('cl4login');

			// set the template page_title (see Controller_Base for implementation)
			$this->template->page_title = 'Forgot Password';

			if (isset($_POST['reset_username'])) {
				// If recaptcha is valid and is received
				$captcha_received = FALSE;
				if (isset($_POST['recaptcha_challenge_field']) && isset($_POST['recaptcha_response_field'])) {
					$captcha_received = TRUE;
					$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);
				}

				$user = ORM::factory('user')->where('username', '=', $_POST['reset_username'])
					->find();

				// Admin passwords cannot be reset by email
				if ($captcha_received && $resp->is_valid && $user->loaded() && ! in_array($user->username, $default_options['admin_accounts'])) {
					// send an email with the account reset token
					$user->set('reset_token', Text::random('alnum', 32))
						->is_valid()
						->save();

					try {
						$mail = new Mail();
						$mail->IsHTML();
						$mail->add_user($user->id);
						$mail->Subject = LONG_NAME . ' Password Reset';

						// build a link with action reset including their username and the reset token
						$url = URL::site(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'reset')) . '?' . http_build_query(array(
							'username' => $user->username,
							'reset_token' => $user->reset_token,
						)), FALSE);

						$mail->Body = View::factory('cl4/cl4login/forgot_link')
							->set('app_name', LONG_NAME)
							->set('url', $url)
							->set('admin_email', ADMIN_EMAIL);

						$mail->Send();

						Message::add(__(Kohana::message('login', 'reset_link_sent')), Message::$notice);
					} catch (Exception $e) {
						Message::add(__(Kohana::message('login', 'forgot_send_error')), Message::$error);
						throw $e;
					}
				} else if (in_array($user->username, $default_options['admin_accounts'])) {
					Message::add(__(Kohana::message('login', 'reset_admin_account')), Message::$warning);

				} else {
					Message::add(__(Kohana::message('login', 'reset_not_found')), Message::$warning);
					if ( ! $captcha_received || ! $resp->is_valid) {
						Message::add(__(Kohana::message('user', 'recaptcha_not_valid')), Message::$warning);
					}
				}
			} // if post

			$this->template->body_html = View::factory('cl4/cl4login/forgot');
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::add(__(Kohana::message('login', 'reset_error')), Message::$error);
		}
	} // function action_forgot

	/**
	* A basic version of "reset password" functionality.
	*
	* @todo consider changing this to not send the password, but instead allow them enter a new password right there; this might be more secure, but since we've sent them a link anyway, it's probably too late for security; the only thing is email is insecure (not HTTPS)
	*/
	function action_reset() {
		try {
			$default_options = Kohana::$config->load('cl4login');

			// set the template title (see Controller_Base for implementation)
			$this->template->page_title = 'Password Reset';

			$username = cl4::get_param('username');
			if ($username !== null) $username = trim($username);
			$reset_token = cl4::get_param('reset_token');

			// make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
			// also make sure we aren't trying to reset the password for an admin
			if ( ! empty($username) && ! empty($reset_token) && strlen($reset_token) == 32) {
				$user = ORM::factory('user')->where('username', '=', $_REQUEST['username'])->and_where('reset_token', '=', $_REQUEST['reset_token'])->find();

				// admin passwords cannot be reset by email
				if (is_numeric($user->id) && ! in_array($user->username, $default_options['admin_accounts'])) {
					try {
						$password = cl4_Auth::generate_password();
						$user->values(array(
								'password' => $password,
								// reset the failed login count
								'failed_login_count' => 0,
								// send the user to the password update page
								'force_update_password_flag' => 1,
							))
							->is_valid()
							->save();
					} catch (Exception $e) {
						Message::add(__(Kohana::message('login', 'password_email_error')), Message::$error);
						throw $e;
					}

					try {
						$mail = new Mail();
						$mail->IsHTML();
						$mail->add_user($user->id);
						$mail->Subject = LONG_NAME . ' New Password';

						// provide a link to the user including their username
						$url = URL::site(Route::get(Route::name(Request::current()->route()))->uri() . '?' . http_build_query(array('username' => $user->username)), FALSE);

						$mail->Body = View::factory('cl4/cl4login/forgot_reset')
							->set('app_name', LONG_NAME)
							->set('username', $user->username)
							->set('password', $password)
							->set('url', $url)
							->set('admin_email', ADMIN_EMAIL);

						$mail->Send();

						Message::add(__(Kohana::message('login', 'password_emailed')), Message::$notice);

					} catch (Exception $e) {
						Message::add(__(Kohana::message('login', 'password_email_error')), Message::$error);
						throw $e;
					}

					Request::current()->redirect(Route::get(Route::name(Request::current()->route()))->uri());

				} else {
					Message::add(__(Kohana::message('login', 'password_email_username_not_found')), Message::$error);
					Request::current()->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'forgot')));
				}

			} else {
				Message::add(__(Kohana::message('login', 'password_email_partial')), Message::$error);
				Request::current()->redirect(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'forgot')));
			}
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::add(__(Kohana::message('login', 'reset_error')), Message::$error);
		}
	} // function action_reset
}