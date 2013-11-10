<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A default base Controller class.
 * Some of the functionality is required by XM and other modules.
 */
class Controller_XM_Base extends Controller_Template {
		/**
	 * The template to use. The string is replaced with the View in before().
	 * @var  View
	 */
	public $template = 'base/template'; // this is the default template file

	/**
	 * The current page. Adds a class to the body, ie, "p_page_name".
	 * @var  string
	 */
	public $page;

	/**
	 * Controls access for the whole controller.
	 * If the entire controller REQUIRES that the user be logged in, set this to TRUE.
	 * If some or all of the controller DOES NOT need to be logged in, set to this FALSE; to control which actions require authentication or a specific permission, us the $secure_actions array.
	 */
	public $auth_required = FALSE;

	/**
	 * Controls access for separate actions
	 *
	 * Examples:
	 * not set (FALSE) => when $auth_required is TRUE, then it will be considered a secure action, but will only require that the user is logged in
	 *            when $auth_required is FALSE, then everyone will have access to the action
	 * 'list' => FALSE the list action does not require the user to be logged in (the following are all the same as FALSE: "", 0, "0", NULL, array() (empty array))
	 * 'profile' => TRUE allows any logged in user to access that action
	 * 'adminpanel' => 'admin' will only allow users with the permission admin to access action_adminpanel
	 * 'moderatorpanel' => array('login', 'moderator') will only allow users with the permissions login AND moderator to access action_moderatorpanel
	 */
	public $secure_actions = FALSE;

	/**
	 * An array of actions as found in the request that shouldn't use auto_render.
	 * @var  array
	 */
	protected $no_auto_render_actions = array();

	/**
	 * If the messages should be added to the template.
	 * @var  boolean
	 */
	protected $display_messages = TRUE;

	/**
	 * Array of scripts, keyed by name with value of array containing keys path, media and array of required styles before adding this style.
	 * Add to using add_style().
	 * @var  array
	 */
	protected $styles = array();

	/**
	 * Array of scripts, keyed by name with value of array containing keys path and array of required scripts before adding this script.
	 * Add to using add_script().
	 * @var  array
	 */
	protected $scripts = array();

	/**
	 * String of on load JavaScript.
	 * @var  array
	 */
	protected $on_load_js = '';

	/**
	 * The default page title append (added after the rest of the page title).
	 * Not added by default, but can be used to make changing it later easier.
	 * Defaults to the LONG_NAME constant.
	 * @var  string
	 */
	protected $page_title_append = LONG_NAME;

	/**
	 * Automatically executed before the controller action. Can be used to set
	 * class properties, do authorization checks, and execute other custom code.
	 * Logs the request if the user is logged in.
	 * Disabled auto render if the action is in the no_auto_render_actions array.
	 * Checks to see if the site is currently unavailable and then throws a 503.
	 * Checks the login based on the auth_required and secure_actions properties.
	 * Initializes the template.
	 *
	 * @return  void
	 */
	public function before() {
		try {
			// only log the request if they're logged in
			if (Auth::instance()->logged_in()) {
				Model_Request_Log::store_request();
			}
		} catch (Exception $e) {
			Kohana_Exception::handler_continue($e);
		}

		if (in_array($this->request->action(), $this->no_auto_render_actions)) {
			$this->auto_render = FALSE;
		}

		// if the site is unavailable, redirect the user to the unavailable page
		if (defined('UNAVAILABLE_FLAG') && UNAVAILABLE_FLAG) {
			throw HTTP_Exception(503, __('The site is currently unavailable.'));
		}

		parent::before();

		$this->check_login();

		// set up the default template values for the base template
		$this->initialize_template();
	} // function before

	/**
	 * Automatically executed after the controller action. Can be used to apply
	 * transformation to the request response, add extra output, and execute
	 * other custom code.
	 * Completes the setup of the template.
	 *
	 * @return  void
	 */
	public function after() {
		if ($this->auto_render) {
			// add a body class for page
			if ( ! empty($this->page)) {
				$this->template->body_class .= ' p_' . $this->page;
			}

			if ( ! empty($this->styles)) {
				$this->template->styles = $this->compile_styles();
			} // if

			if ( ! empty($this->scripts)) {
				$this->template->scripts = $this->compile_scripts();
			}

			$this->template->on_load_js = $this->on_load_js;

			// look for any status message and display
			if ($this->display_messages) {
				$this->template->message = Message::display();
			}

			if (XM::is_dev()) {
				// this is so a session isn't started needlessly when in debug mode
				$this->template->session = Session::instance()->as_array();
			}
		} // if

		parent::after();
	} // function after

	/**
	 * Setup the default template values.
	 *
	 * @return void
	 */
	protected function initialize_template() {
		if ($this->auto_render) {
			// Initialize default values
			$this->template->logged_in = Auth::instance()->logged_in();
			if ($this->template->logged_in) {
				$this->template->user = Auth::instance()->get_user();
			}

			$this->add_template_styles()
				->add_template_js();

			// set some empty variables
			$this->template->page_title = '';
			$this->template->meta_tags = array();
			$this->template->body_class = '';
			$this->template->pre_message = '';
			$this->template->body_html = '';
		} // if
	}

	/**
	 * Checks if the user is logged in and if they have permissions to the current action
	 * If the user is not logged in, then they are redirected to the timed out page or login page
	 * If the user is logged in, but not allowed, then they are sent to the no access page
	 * If they are logged in and have access, then it will updat the timestamp in the session
	 * If c_ajax == 1, then a JSON string will be returned instead, using AJAX_Status and it's constants
	 *
	 * @return  Controller_Base
	 */
	public function check_login() {
		// ***** Authentication *****
		// check to see if they are allowed to access the action
		if ( ! Auth::instance()->controller_allowed($this, $this->request->action())) {
			$is_ajax = (bool) Arr::get($_REQUEST, 'c_ajax', FALSE);

			if (Auth::instance()->logged_in()) {
				// user is logged in but not allowed to access the page/action
				if ($is_ajax) {
					echo AJAX_Status::ajax(array(
						'status' => AJAX_Status::NOT_ALLOWED,
						'debug_msg' => 'Referrer: ' . $this->request->uri(),
					));
					exit;
				} else {
					$this->redirect(Route::get('login')->uri(array('action' => 'noaccess')) . $this->get_login_redirect_query());
				}
			} else {
				if (Auth::instance()->timed_out()) {
					if ($is_ajax) {
						echo AJAX_Status::ajax(array(
							'status' => AJAX_Status::TIMEDOUT,
						));
						exit;
					} else {
						// store the get and post if timeout post is enabled
						$this->process_timeout();

						// display password page because the sesion has timeout
						$this->redirect(Route::get('login')->uri(array('action' => 'timedout')) . $this->get_login_redirect_query());
					}
				} else {
					if ($is_ajax) {
						// just not logged in and is ajax so return a json array with the status of not logged in
						echo AJAX_Status::ajax(array(
							'status' => AJAX_Status::NOT_LOGGED_IN,
						));
						exit;
					} else {
						// just not logged in, so redirect them to the login with a redirect parameter back to the current page
						$this->redirect(Route::get('login')->uri() . $this->get_login_redirect_query());
					}
				}
			} // if
		} // if

		if (Auth::instance()->logged_in() && $this->auto_render) {
			// update the session auth timestamp
			Auth::instance()->update_timestamp();
		} // if

		return $this;
	} // function check_login

	/**
	 * Returns the query containing the redirect for the login controller.
	 * Used within the check_login() method to pass the redirect through the login action/controller.
	 * Bases the redirect on current URL/URI and the full get/query string.
	 *
	 * @return  string
	 */
	protected function get_login_redirect_query() {
		return URL::array_to_query(array('redirect' => $this->request->uri() . '?' . http_build_query($_GET)), '&');
	}

	/**
	 * If the login timeout post functionality is enabled, this will store the passed
	 * GET and POST in the session key for use in Controller_XM_Login to re-post the data.
	 * If there is no get or post, this will unset the session key
	 *
	 * @return  void
	 */
	protected function process_timeout() {
		if (Kohana::$config->load('xm_login.enable_timeout_post')) {
			// store the post so we can post it again after the user enters their password
			$timeout_post_session_key = Kohana::$config->load('xm_login.timeout_post_session_key');
			$query = $this->request->query();
			$post = $this->request->post();
			if ( ! empty($query) || ! empty($post)) {
				Session::instance()->set($timeout_post_session_key, array(
					'post_to' => $this->request->uri(),
					'get' => $query,
					'post' => $post,
				));
			} else {
				Session::instance()->delete($timeout_post_session_key);
			}
		} // if
	} // function process_timeout

	/**
	 * Adds the base style, which is the compiled version of the SASS files.
	 *
	 * @return  Controller_Base
	 */
	public function add_template_styles() {
		$this->add_style('base', 'css/base.css');

		return $this;
	} // function add_template_styles

	/**
	 * Sets up the template script var, add's jquery, jquery ui, and base.js if they are not already set.
	 * If not in dev, base.min.js will be added instead of base.
	 *
	 * @return  Controller_Base
	 */
	public function add_template_js() {
		$this->add_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js')
			->add_script('jquery_ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js');
		if (DEBUG_FLAG) {
			$this->add_script('xm_debug', 'xm/js/debug.js');
		}
		if (XM::is_dev()) {
			$this->add_script('base', 'js/base.js');
		} else {
			$this->add_script('base', 'js/base.min.js');
		}

		return $this;
	} // function add_template_js

	/**
	 * Adds JavaScript to the template on_load_js var, including checking to see if there should be a line break before the addition.
	 *
	 * @param  string  $js  The javascript to add
	 * @return  Controller_Base
	 */
	public function add_on_load_js($js) {
		if ( ! empty($this->on_load_js)) {
			$this->on_load_js .= "\n";
		}
		$this->on_load_js .= $js;

		return $this;
	} // function add_on_load_js

	/**
	 * Adds a CSS file.
	 * If $replace is FALSE, the CSS file will not be added again based on the name.
	 * If $replace is TRUE, the CSS file will replace the existing CSS file.
	 *
	 * @param  string  $name   The name of the CSS file.
	 * @param  string  $path   The path to the CSS file.
	 * @param  string  $media  The media type. NULL for all/none.
	 * @param  array   $dependencies  Array of CSS file names that are dependencies before this one can be loaded.
	 * @param  boolean  $replace  Controls if the CSS file should replace an existing file.
	 * @return  Controller_Base
	 */
	protected function add_style($name, $path, $media = NULL, $dependencies = array(), $replace = FALSE) {
		if ( ! isset($this->styles[$name]) || $replace) {
			$this->styles[$name] = array(
				'path' => $path,
				'media' => $media,
				'dependencies' => $dependencies,
			);
		}

		return $this;
	}

	/**
	 * Adds a script file.
	 * If $replace is FALSE, the script file will not be added again based on the name.
	 * If $replace is TRUE, the script file will replace the existing script file.
	 *
	 * @param  string  $name  The name of the script file.
	 * @param  string  $path  The path to the script file.
	 * @param  array   $dependencies  Array of script file names that are dependencies before this one can be loaded.
	 * @param  boolean  $replace  Controls if the script file should replace an existing file.
	 * @return  Controller_Base
	 */
	protected function add_script($name, $path, $dependencies = array(), $replace = FALSE) {
		if ( ! isset($this->scripts[$name]) || $replace) {
			$this->scripts[$name] = array(
				'path' => $path,
				'dependencies' => $dependencies,
			);
		}

		return $this;
	}

	/**
	 * Compiles the array of styles with the key being the path and the value being the media type
	 * ordering the array based on the required scripts.
	 *
	 * @return  array
	 */
	protected function compile_styles() {
		$styles = $this->compile_assets($this->styles);

		// create the array for use in the template
		$final_styles = array();
		foreach ($styles as $data) {
			$final_styles[$data['path']] = $data['media'];
		}

		return $final_styles;
	}

	/**
	 * Compiles the array of scripts where the key is the name and the value is the path
	 * ordering the array based on the required scripts.
	 *
	 * @return  array
	 */
	protected function compile_scripts() {
		$scripts = $this->compile_assets($this->scripts);

		// create the array for use in the template
		$final_scripts = array();
		foreach ($scripts as $name => $data) {
			$final_scripts[$name] = $data['path'];
		}

		return $final_scripts;
	}

	/**
	 * Loops through the array, ordering it based on the required values.
	 * 'required' must be one of the keys in the value array.
	 * Taken from [Kohana-Assets](https://github.com/coreyworrell/Kohana-Assets/blob/master/classes/assets/core.php#L346).
	 *
	 * @param  array  $assets  The assets to order.
	 * @return  array
	 */
	protected function compile_assets($assets) {
		$original = $assets;
		$compiled = array();

		while (count($assets) > 0) {
			foreach ($assets as $name => $value) {
				// No dependencies anymore, add it to compiled
				if (empty($assets[$name]['dependencies'])) {
					$compiled[$name] = $value;
					unset($assets[$name]);
				} else {
					foreach ($assets[$name]['dependencies'] as $k => $v) {
						// Remove dependency if doesn't exist, if its dependent on itself, or if the dependent is dependent on it
						if ( ! isset($original[$v]) || $v === $name || (isset($assets[$v]) && in_array($name, $assets[$v]['dependencies']))) {
							unset($assets[$name]['dependencies'][$k]);
							continue;
						}

						// This dependency hasn't been compiled yet
						if ( ! isset($compiled[$v]))
							continue;

						// This dependency is taken care of, remove from list
						unset($assets[$name]['dependencies'][$k]);
					} // foreach
				} // if
			} // foreach
		} // while

		return $compiled;
	} // function compile_assets
}