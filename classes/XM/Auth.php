<?php defined('SYSPATH') or die('No direct access allowed.');

class XM_Auth extends Kohana_Auth_ORM {
	/**
	* An array of permissions that have already been checked
	* permission => bool (has, doesn't have)
	*
	* @var 	array
	*/
	protected $permissions = array();

	/**
	 * Loads Session and configuration options.
	 * Checks for 3.0.x hashing config option and sets the salt pattern if it's enabled
	 *
	 * @return  void
	 */
	public function __construct($config = array()) {
		if ($config['enable_3.0.x_hashing']) {
			// Clean up the salt pattern and split it into an array
			$config['salt_pattern'] = preg_split('/,\s*/', Kohana::$config->load('auth')->get('salt_pattern'));
		}

		parent::__construct($config);
	} // function __construct

	/**
	* Checks if a session is active.
	*
	* @param   mixed    permission string or array of permissions (roles)
	* @param   boolean  check user for every role applied (TRUE, by default) or if any?
	* @return  boolean
	*/
	public function logged_in($permission = NULL, $all_required = TRUE) {
		// Get the user from the session
		$user = $this->get_user();

		if ( ! $user) {
			return FALSE;
		}

		// the session user object is an instance of Model_User and the session has not timed out
		if ($user instanceof Model_User && $user->loaded() && ! $this->timed_out()) {
			// Everything is okay so far
			if (empty($permission)) {
				return TRUE;
			}

			if ( ! empty($permission)) {
				return $this->allowed($permission, NULL, $all_required);
			}
		}

		return FALSE;
	} // function logged_in

	/**
	 * Log out a user by removing the related session variables.
	 * Also removes the auth timestamp_key
	 *
	 * @param   boolean  completely destroy the session
	 * @param   boolean  remove all tokens for user
	 * @return  boolean
	 */
	public function logout($destroy = FALSE, $logout_all = FALSE) {
		$this->_session->delete($this->_config['timestamp_key']);

		if ($this->get_user()) {
			$this->get_user()->add_auth_log(Model_Auth_Log::LOG_TYPE_LOGGED_OUT);
		}

		return parent::logout($destroy, $logout_all);
	} // function logout

	/**
	* Checks to see if the currently logged in user has access to the specific permission
	*
	* @param 	mixed	$permission		If a string, then the user is required to have that permission; if it's an array then they need to have all the permissions
	* 									If an object and a sub class of Controller_Base then it will use controller_allowed() and base the permission checking on the vars within that controller
	* @param	string	$action_name	If $permission is a object then this needs to be the action to test against
	* @param    boolean  check user for every role applied (TRUE, by default) or if any?
	* @return	boolean
	*/
	public function allowed($permission, $action_name = NULL, $all_required = TRUE) {
		$status = FALSE;

		if (is_object($permission) && $permission instanceof Controller_Base && $action_name !== NULL) {
			return $this->controller_allowed($permission, $action_name);

		} else {
			// Get the user from the session
			$user = $this->get_user();

			// check to see if we are logged in (don't sent permission so we don't end up in a loop)
			if ($this->logged_in(NULL)) {
				// Everything is okay so far

				// Multiple permissions to check
				if (is_array($permission)) {
					$has_all_permissions = TRUE;
					$has_one_permission = FALSE;
					// Check each permission
					foreach ($permission as $_permission) {
						// Check to see if we the permission is already stored so we don't need to check in the DB
						if (array_key_exists($_permission, $this->permissions)) {
							if ( ! $this->permissions[$_permission]) {
								$has_all_permissions = FALSE;
							} else {
								$has_one_permission = TRUE;
							}

						} else {
							// If the user doesn't have the permission
							if ( ! $user->permission($_permission)) {
								// Set the status false and get outta here
								$this->permissions[$_permission] = FALSE;
								$has_all_permissions = FALSE;
							} else {
								$this->permissions[$_permission] = TRUE;
								$has_one_permission = TRUE;
							}
						} // if
					} // foreach

					// if the user has all the permissions passed, set the status to true
					if ($has_all_permissions && $all_required) {
						$status = TRUE;
					} else if ($has_one_permission && ! $all_required) {
						$status = TRUE;
					}

				} else {
					// Single permission to check
					// Check that the user has the given permission
					if (array_key_exists($permission, $this->permissions)) {
						if ($this->permissions[$permission]) {
							$status = TRUE;
						}

					} else {
						// Store the value in the permission array
						if ( ! $user->permission($permission)) {
							$this->permissions[$permission] = FALSE;
						} else {
							$this->permissions[$permission] = TRUE;
							$status = TRUE;
						}
					}
				} // if
			} // if
		} // if

		return $status;
	} // function

	/**
	* Checks the permissions of the user based on the $auth_required and $secure_actions in the controller that would be doing the request
	*
	* Here are the use cases related to the controller:
	*  - public: entire controller is public
	*     auth_required = FALSE (everything else ignored)
	*  - logged in: user must be logged in, although no other permissions required (something like the account controller)
	*     auth_required = TRUE && secure_actions = FALSE
	*  - logged in + own checking: the same as above, but you are doing your own checking within the controller (like xmadmin)
	*     auth_required = TRUE && secure_actions = FALSE
	*  - logged in + specific permission(s): a specific permission is required to access the action; with multiple permissions all of them are required
	*     auth_required = TRUE && (secure_action['action'] = 'perm' || secure_action['action'] = array('perm1', 'perm2'))
	*  - logged in + specific action can be accessed by anyone: must be logged in, but the specific permission is accessible to anyone, while other permissions have specific permissions (works in conjunction with previous one)
	*     auth_required = TRUE && secure_action = FALSE
	*
	* @param 	mixed 	$controller		The name of the controller (only the suffix, Account of Controller_Account) or the controller object
	* @param 	string 	$action_name	The action to check the permissions against
	* @return	bool
	*/
	public function controller_allowed($controller, $action_name) {
		if ( ! is_object($controller)) {
			// $controller is not an object so we want to try to create the controller to get the permissions from the controller
			$controller = 'Controller_' . $controller;
			$controller = new $controller(Request::current());
		}

		// no auth required
		if ($controller->auth_required === FALSE) {
			// allowed: public controller
			return TRUE;
		}

		$logged_in = $this->logged_in();

		// auth is required AND the user is not logged in
		if ($controller->auth_required === TRUE && ! $logged_in) {
			// not allowed
			return FALSE;
		}

		// auth is required AND logged in AND secure actions is set to FALSE (the default)
		if ($controller->auth_required === TRUE && $logged_in && $controller->secure_actions === FALSE) {
			// allowed: likely doing own checking or entire controller is allowed to anyone logged in
			return TRUE;
		}

		$secure_actions_is_array = is_array($controller->secure_actions);
		if ($secure_actions_is_array) {
			$action_set = isset($controller->secure_actions[$action_name]);
		} else {
			// the action cannot be set because secure_actions is not an array
			$action_set = FALSE;
		}

		// auth is required AND logged in AND secure actions is an array AND the action is not set in the array
		if ($controller->auth_required === TRUE && $logged_in && $secure_actions_is_array && ! $action_set) {
			// allowed
			return TRUE;
		}

		// auth is required AND logged in AND secure actions is an array AND the value of the key is a string (a permission) AND the user has the permission
		if ($controller->auth_required === TRUE && $logged_in && $secure_actions_is_array && $action_set && (is_string($controller->secure_actions[$action_name]) || is_array($controller->secure_actions[$action_name])) && $this->logged_in($controller->secure_actions[$action_name])) {
			return TRUE;
		}

		// the controller has auth required, but the action does not require authentication
		// auth is required AND logged in AND secure actions is an array AND the value of the key is a string AND action is FALSE
		if ($controller->auth_required === TRUE && $logged_in && $secure_actions_is_array && $action_set && $controller->secure_actions[$action_name] === FALSE) {
			return TRUE;
		}

		// not allowed
		return FALSE;
	} // function controller_allowed

	/**
	* Checks to see if the user has timed out based on the timestamp in the session
	* To use this, make sure the session key in timestamp_key is set on each page access (if the user is logged in)
	* Returns FALSE if the user HAS NOT timed out
	* Return TRUE if the user HAS timed out
	*
	* @param 	int		$auth_lifetime	The maximum lifetime to check for; leave as default of NULL to check based on the config for auth_lifetime; set to 0 for no timeout
	* @return	bool
	*/
	public function timed_out($auth_lifetime = NULL) {
		if ($auth_lifetime === null) $auth_lifetime = $this->_config['auth_lifetime'];
		$current_timestamp = Session::instance()->get($this->_config['timestamp_key'], 0);
		// there is no auth lifetime or no timestamp in the session or the current timestamp plus the lifetime is in the future or now
		if ($current_timestamp == 0 || $auth_lifetime == 0 || ($current_timestamp > 0 && ($current_timestamp + $auth_lifetime) >= time())) {
			// session has not timed out
			return FALSE;
		} else {
			// they have timed out
			return TRUE;
		}
	} // function timed_out

	/**
	* Updates the session timestamp with the current time (in seconds)
	*/
	public function update_timestamp() {
		Session::instance()->set($this->_config['timestamp_key'], time());
	} // function update_timestamp

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean  TRUE on success or FALSE on failure
	 * @return  array    An array of messages meaning there were errors and message that should be displayed; each key in the array is another message and the value is an array, where the first key is the field and the second is the number of attempts
	 */
	public function login($username, $password, $remember = FALSE, $verified_human = FALSE) {
		if (empty($password)) {
			$user = ORM::factory('User');

			$user->add_auth_log(Model_Auth_Log::LOG_TYPE_INVALID_PASSWORD, $username);

			$labels = $user->labels();
			return array(
				array('password.not_empty', array(':field' => $labels['password'])),
			);
		}

		if (is_string($password)) {
			if ($this->_config['enable_3.0.x_hashing']) {
				// Get the salt from the stored password
				$salt = $this->find_salt($this->password($username));

				// Create a hashed password using the salt from the stored password
				$password = $this->hash_password($password, $salt);
			} else {
				$password = $this->hash($password);
			}
		}

		return $this->_login($username, $password, $remember, $verified_human);
	}

	/**
	* Logs a user in.
	* Same as Auth_ORM::_login(), but doesn't check for login role (no role/permission checking)
	*
	* @param   string   username
	* @param   string   password
	* @param   boolean  enable autologin
	* @return  boolean  TRUE on success or FALSE on failure
	* @return  array    An array of messages meaning there were errors and message that should be displayed; each key in the array is another message and the value is an array, where the first key is the field and the second is the number of attempts
	*/
	protected function _login($user, $password, $remember, $verified_human = FALSE) {
		$messages = array();
		$login_config = Kohana::$config->load('xmlogin');

		// user is not an object, so it must be the username
		if ( ! is_object($user)) {
			$username = $user;

			// Load the user
			$user = ORM::factory('User');
			$user->add_login_where($username)->find();

		// $user passed as an object, so we want to grab the username from the object
		// note: Kohana doesn't do this because they don't actually need the username var anywhere, they just store it just incase
		} else {
			$username = $user->username;
		}

		$user_labels = $user->labels();

		// we found a user, but their account has too many login attempts and we haven't verified that they're human
		if ($user->loaded() && $this->too_many_login_attempts($user->failed_login_count) && ! $verified_human) {
			// increment the failed login count
			$user->increment_failed_login();

			// set the session key that forces a captcha
			$login_session = Session::instance()->get($login_config['session_key'], array());
			$login_session['force_captcha'] = TRUE;
			Session::instance()->set($login_config['session_key'], $login_session);

			// add a message and set the auth type for logging
			$messages[] = array('username.too_many_attempts', array(':field' => $user_labels['username']));
			$auth_type = Model_Auth_Log::LOG_TYPE_TOO_MANY_ATTEMPTS;

		// If the passwords match, perform a login
		} else if ($user->loaded() && $user->password === $password) {
			if ($remember === TRUE) {
				// Token data
				$data = array(
					'user_id'    => $user->id,
					'expires'    => time() + $this->_config['lifetime'],
					'user_agent' => sha1(Request::$user_agent),
				);

				// Create a new autologin token
				$token = ORM::factory('User_Token')
							->values($data)
							->create();

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			} // if

			// Finish the login
			$this->complete_login($user);

			// add the auth log entry
			$user->add_auth_log(Model_Auth_Log::LOG_TYPE_LOGGED_IN, $username);

			return TRUE;

		// user is loaded, means that the user exists
		} else if ($user->loaded()) {
			$user->increment_failed_login();
			$auth_type = Model_Auth_Log::LOG_TYPE_INVALID_PASSWORD;
			$messages[] = array('username.invalid', array());
			Message::add('User found, but password incorrect', Message::$debug);

		// no user loaded, so the username and password must be wrong
		} else {
			$auth_type = Model_Auth_Log::LOG_TYPE_INVALID_USERNAME_PASSWORD;
			$messages[] = array('username.invalid', array());
			Message::add('User not found', Message::$debug);
		}

		$user->add_auth_log($auth_type, $username);

		// Login failed
		if ( ! empty($messages)) {
			return $messages;
		} else {
			return FALSE;
		}
	} // function _login

	/**
	* Determine if the current user has too many login attempts and therefore is required to enter a captcha
	* Returns TRUE if they do, FALSE if they don't
	*
	* @return  boolean
	*/
	public function too_many_login_attempts($failed_login_count) {
		$login_config = Kohana::$config->load('xmlogin');
		return ($failed_login_count !== NULL && $failed_login_count > $login_config['max_failed_login_count']);
	}

	/**
	* Generates a random password without any characters that can be confused `$length` characters long.
	*
	* @param  int  $length  The length to generate, defaults to 15.
	*
	* @return  string  The random password
	*/
	public static function generate_password($length = 15) {
		return Text::random('distinct', $length);
	}

	/**
	* This function is run after the login completes
	* Add any session setting that is needed after the user logs in here
	* The User Model is already stored in the session
	* parent::complete_login() should always be called after (or before) as this puts the user model in the session
	* Removes the login session key
	*
	* @param   ORM  user ORM object
	*
	* @return  void
	*/
	protected function complete_login($user) {
		$this->update_timestamp();

		// delete the session key that contains # of attempts and forced captcha flag
		Session::instance()->delete(Kohana::$config->load('xmlogin.session_key'));

		return parent::complete_login($user);
	} // function complete_login

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username string, or user ORM object
	 * @return  string
	 */
	public function password($user) {
		if ( ! is_object($user)) {
			$username = $user;

			// Load the user
			$user = ORM::factory('User');
			$user->add_login_where($username)
				->find();
		}

		return $user->password;
	} // function password

	/**
	 * Perform a hash, using the configured method.
	 * Optionally uses the Kohana 3.0.x hashing.
	 * Override this method if you have old passwords under another hashing method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str) {
		if ($this->_config['enable_3.0.x_hashing']) {
			return $this->hash_password($str);
		} else {
			return parent::hash($str);
		}
	} // function hash

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * [!!!] For use with passwords generated under Kohana 3.0.x
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE) {
		if ($salt === FALSE) {
			// Create a salt seed, same length as the number of offsets in the pattern
			$salt = substr(hash($this->_config['hash_method'], uniqid(NULL, TRUE)), 0, count($this->_config['salt_pattern']));
		}

		// Password hash that the salt will be inserted into
		$hash = hash($this->_config['hash_method'], $salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->_config['salt_pattern'] as $offset) {
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	} // function hash_password

	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * [!!!] For use with passwords generated under Kohana 3.0.x
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password) {
		$salt = '';

		foreach ($this->_config['salt_pattern'] as $i => $offset) {
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	} // function find_salt
}