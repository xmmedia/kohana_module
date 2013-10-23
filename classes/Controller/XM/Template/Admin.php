<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is a template for other admins.
 * Extend this and make the necessary changes.
 * This shouldn't be used directly; it will likely break and be a security hole.
 */
class Controller_XM_Template_Admin extends Controller_Private {
	public $page = 'cl4admin';

	// true means users must be logged in to access this controller
	public $auth_required = TRUE;
	// secure actions: actions that require you to more than just logged in
	// the list is currently commented out so all actions will not have any special permission checking done
	// copy the list and add permissions as necessary
	public $secure_actions = array(
		// 'index' => '',
		// 'add' => '',
		// 'add_multiple' => '',
		// 'cancel_search' => '',
		// 'delete' => '',
		// 'download' => '',
		// 'edit' => '',
		// 'edit_multiple' => '',
		// 'search' => '',
		// 'view' => '',
		// 'export' => '',
	);
	protected $no_auto_render_actions = array('download', 'export');

	protected $model_name; // the name of the model currently being manipulated
	protected $model_display_name = 'Model Name'; // the fulll, friendly object name as specified in the options or the model itself
	/**
	* @var  ORM  The model we are working with.
	*/
	protected $model; // the actual model object for $model_name

	protected $id;

	// stores the values in the session for the current model (by reference)
	protected $session_key = 'model_session';
	protected $controller_session;
	protected $page_offset = 1;
	protected $search;
	protected $sort_column;
	protected $sort_order;
	protected $default_session = array(
		'sort_by_column' => NULL, // orm defaults to primary key
		'sort_by_order' => NULL, // orm defaults to DESC
		'page' => 1,
		'search' => NULL,
	);

	/**
	 * @var  array  Custom options for the edtiable list options passed to MultiORM and merged with defaults in display_editable_list().
	 **/
	protected $editable_list_options = array();

	/**
	 * @var  array  Custom options for the passed to MultiORM and merged with defaults in display_editable_list().
	 **/
	protected $multiorm_options = array();

	protected $route = 'model_route';

	protected $default_view = 'cl4/cl4admin/admin';

	/**
	* Runs before the action.
	* Calls parent::before().
	*/
	public function before() {
		parent::before();

		$this->page_title_append = 'Administration - ' . $this->page_title_append;

		// set the information from the route/get/post parameters
		$this->id = $this->request->param('id');
		$page_offset = $this->request->query('page');
		$sort_column = $this->request->query('sort_by_column');
		$sort_order = $this->request->query('sort_by_order');

		$this->controller_session = (array) Session::instance()->get($this->session_key);
		$this->controller_session += $this->default_session;

		// check to see if anything came in from the page parameters
		// if we did, then set it in the session
		if ($page_offset !== NULL) $this->controller_session['page'] = $page_offset;
		if ($sort_column !== NULL) $this->controller_session['sort_by_column'] = $sort_column;
		if ($sort_order !== NULL) $this->controller_session['sort_by_order'] = $sort_order;

		// set the values in object from the values in the session
		$this->page_offset = $this->controller_session['page'];
		$this->sort_column = $this->controller_session['sort_by_column'];
		$this->sort_order = $this->controller_session['sort_by_order'];
		$this->search = ( ! empty($this->controller_session['search']) ? $this->controller_session['search'] : NULL);

		$this->add_css();
	} // function before

	protected function add_css() {
		if ($this->auto_render) {
			$this->add_style('private', 'css/private.css')
				->add_style('dbadmin', 'css/dbadmin.css');
		}
	} // function add_css

	/**
	* Stores the current values for page, search and sorting in the session.
	*/
	public function after() {
		$this->controller_session['page'] = $this->page_offset;
		$this->controller_session['sort_by_column'] = $this->sort_column;
		$this->controller_session['sort_by_order'] = $this->sort_order;
		$this->controller_session['search'] = $this->search;

		$this->save_session();

		parent::after();
	} // function after

	/**
	* Load the model
	*
	* @param  string  $mode  The mode to load the model in (view, edit, add, search, etc)
	*/
	protected function load_model($mode = 'view') {
		$this->model = ORM::factory($this->model_name, $this->id)
			->set_mode($mode);
	} // function load_model

	/**
	* The default action
	* Just displays the editable list using display_editable_list()
	*/
	public function action_index() {
		$this->template->page_title = $this->page_title_append;
		$this->display_editable_list();
	}

	/**
	* Display the editable list of records for the selected object.
	*/
	public function display_editable_list() {
		// set up the admin options
		$options = array(
			'mode' => 'view',
			'sort_by_column' => $this->sort_column,
			'sort_by_order' => $this->sort_order,
			'page_offset' => $this->page_offset,
			'in_search' => ( ! empty($this->search) || ! empty($this->sort_column)),
			'editable_list_options' => array(
				'per_row_links' => array(
					'view' => TRUE,     // view button
					'edit' => TRUE,     // edit button
					'delete' => TRUE,   // delete button
					'add' => TRUE,      // add (duplicate) button
					'checkbox' => TRUE, // checkbox
				),
				'top_bar_buttons' => array(
					'add' => TRUE,             // add (add new) button
					'add_multiple' => TRUE,    // add multiple button
					'edit' => TRUE,            // edit (edit selected) button
					'export_selected' => TRUE, // export selected button
					'export_all' => TRUE,      // export all button
					'search' => TRUE,          // search button
				),
			),
		);
		$options = Arr::merge($options, array('editable_list_options' => $this->editable_list_options), $this->multiorm_options);

		$orm_multiple = new MultiORM($this->model_name, $options);

		// there is a search so apply it
		if ( ! empty($this->search)) {
			$orm_multiple->set_search($this->search);
		}

		$view_content = $orm_multiple->get_editable_list($options);

		$this->add_default_view('', $view_content);
	} // function display_editable_list

	/**
	* Adds the admin view to $this->template->body_html, setting the title and content
	*
	* @param  string  $title    The title to use in the view
	* @param  string  $content  The content to put in the content container in the view
	*/
	protected function add_default_view($title, $content) {
		$this->template->body_html .= View::factory($this->default_view)
			->bind('title', $title)
			->bind('content', $content);
	} // function add_default_view

	/**
	* Returns the page title based on a message file, merged with the display name of the model
	* Used in conjunction with add_default_view()
	*
	* @param  string   $message_path  The path as used by Kohana::message() to the location of the message
	* @param  mixed    $display_name
	* @return string   The title of the page
	*/
	protected function get_page_title_message($message_path, $display_name = NULL) {
		if ($display_name === NULL) {
			$display_name = $this->model_display_name;
		}

		return __(Kohana::message('cl4admin', $message_path), array(':display_name' => HTML::chars($display_name)));
	} // function get_page_title_message

	/**
	* Cancel the current action by redirecting back to the index action
	*/
	public function action_cancel() {
		// add a notice to be displayed
		Message::message('cl4admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_index();
	} // function

	/**
	* Display an add form or add (save) a new record
	*/
	public function action_add() {
		$this->load_model('add');

		if ( ! empty($_POST)) {
			$this->save_model();
		}

		$view_title = $this->get_page_title_message('adding_item');

		// display the edit form
		$form_options = array(
			'mode' => 'add',
		);
		if ( ! empty($this->id)) {
			// set the form action because the current url includes the id of the record which will cause an update, not an add
			$form_options['form_action'] = URL::site($this->request->route()->uri(array('action' => 'add'))) . URL::query();
		}

		$this->template->page_title = 'Add - ' . $this->page_title_append;
		$view_content = $this->model->get_form($form_options);
		$this->add_default_view($view_title, $view_content);
	} // function action_add

	/**
	* Display an edit form for a record or update (save) an existing record
	*/
	public function action_edit() {
		if (empty($this->id)) {
			throw new Kohana_Exception('No ID received for view');
		}

		$this->load_model('edit');

		if ( ! empty($_POST)) {
			$this->save_model();
		}

		$this->template->page_title = 'Edit - ' . $this->page_title_append;
		$view_title = $this->get_page_title_message('editing_item');
		$view_content = $this->model->get_form(array(
			'mode' => 'edit',
		));
		$this->add_default_view($view_title, $view_content);
	} // function action_edit

	/**
	* Used by add and edit to save (insert or update) the record
	*/
	public function save_model() {
		try {
			// save the record
			$this->model->save_values()->save();
			Message::message('cl4admin', 'item_saved', NULL, Message::$notice);
			$this->redirect_to_index();
		} catch (ORM_Validation_Exception $e) {
			Message::message('cl4admin', 'values_not_valid', array(
				':validation_errors' => Message::add_validation_errors($e, $this->model_name)
			), Message::$error);
		}
	} // function save_model

	/**
	* Views the record in a similar fashion to an edit, but without actual input fields
	*/
	public function action_view() {
		if (empty($this->id)) {
			throw new Kohana_Exception('No ID received for view');
		}

		$this->load_model('view');

		$this->template->page_title = 'View - ' . $this->page_title_append;
		$this->add_default_view(HTML::chars($this->model_display_name), $this->model->get_view());
	} // function

	/**
	* Add and save/insert multiple records
	*/
	public function action_add_multiple() {
		// Create a new MuliORM for this model
		$orm_multiple = MultiORM::factory($this->model_name, array('mode' => 'add'));

		// If form was submitted
		if ( ! empty($_POST)) {
			try {
				$orm_multiple->save_values()->save();
				Message::message('cl4admin', 'multiple_saved', array(':records_saved' => $orm_multiple->records_saved()), Message::$notice);
				$this->redirect_to_index();
			} catch (ORM_Validation_Exception $e) {
				$validation_exceptions = $orm_multiple->validation_exceptions();
				foreach ($validation_exceptions as $num => $exception) {
					Message::message('cl4admin', 'values_not_valid_multiple', array(
						':record_number' => ($num + 1),
						':validation_errors' => Message::add_validation_errors($exception)
					), Message::$error);
				}
			}
		} // if

		// Set view details
		$view_title = $this->get_page_title_message('multiple_add_item', $orm_multiple->_table_name_display);

		// The count for the number of records were adding is stored in the ID field
		$count = $this->request->param('id');
		$view_content = $orm_multiple->get_add_multiple($count);

		// Add view to template
		$this->template->page_title = 'Add - ' . $this->page_title_append;
		$this->add_default_view($view_title, $view_content);
	} // function action_add_multiple

	/**
	* Edit and save/update multiple records
	*/
	public function action_edit_multiple() {
		// set up the admin options
		$orm_multiple = MultiORM::factory($this->model_name, array('mode' => 'edit'));

		if (empty($_POST['ids'])) {
			$ids = NULL;

			try {
				$orm_multiple->save_values()->save();
				Message::message('cl4admin', 'multiple_saved', array(':records_saved' => $orm_multiple->records_saved()), Message::$notice);
				$this->redirect_to_index();
			} catch (ORM_Validation_Exception $e) {
				$validation_exceptions = $orm_multiple->validation_exceptions();
				foreach ($validation_exceptions as $num => $exception) {
					Message::message('cl4admin', 'values_not_valid_multiple', array(
						':record_number' => ($num + 1),
						':validation_errors' => Message::add_validation_errors($exception)
					), Message::$error);
				}
			}
		} else {
			$ids = $_POST['ids'];
		} // if

		$this->template->page_title = 'Edit - ' . $this->page_title_append;
		$view_title = $this->get_page_title_message('multiple_edit_item', $orm_multiple->_table_name_display);
		$view_content = $orm_multiple->get_edit_multiple($ids);
		$this->add_default_view($view_title, $view_content);
	} // function action_edit_multiple

	/**
	* Delete a record with a confirm first
	*/
	public function action_delete() {
		if ( ! ($this->id > 0)) {
			Message::message('cl4admin', 'no_id', NULL, Message::$error);
			$this->redirect_to_index();
		} // if

		$this->load_model();

		if ( ! empty($_POST)) {
			// see if they want to delete the item
			if (strtolower($_POST['cl4_delete_confirm']) == 'yes') {
				if ($this->model->delete() == 0) {
					Message::message('cl4admin', 'no_item_deleted', NULL, Message::$error);
				} else {
					Message::message('cl4admin', 'item_deleted', array(':display_name' => HTML::chars($this->model_display_name)), Message::$notice);
					Message::message('cl4admin', 'record_id_deleted', array(':id' => $this->id), Message::$debug);
				} // if
			} else {
				Message::message('cl4admin', 'item_not_deleted', NULL, Message::$notice);
			}

			$this->redirect_to_index();

		} else {
			// the confirmation form goes in the messages
			Message::add(View::factory('cl4/cl4admin/confirm_delete', array(
				'object_name' => $this->model_display_name,
			)));

			$this->template->page_title = 'Delete - ' . $this->page_title_append;
			$this->add_default_view(HTML::chars($this->model_display_name), $this->model->get_view());
		}
	} // function action_delete

	/**
	* Download a file attached to a record, private or public
	* Don't output the HTML header or footer (auto_render = FALSE)
	* Will display a message if there is a problem
	*/
	public function action_download() {
		// get the target column
		$column_name = $this->request->param('column_name');

		$this->load_model();

		// get the target table name
		$table_name = $this->model->table_name();

		// load the record
		if ( ! ($this->id > 0)) {
			throw new Kohana_Exception('No record ID was received, therefore no file could be downloaded');
		} // if

		// get the file name
		$filename = $this->model->$column_name;

		// check to see if the record has a filename
		if ( ! empty($filename)) {
			$this->model->send_file($this->response, $column_name);

		} else if (empty($filename)) {
			echo Kohana::message('cl4admin', 'no_file');
			throw new Kohana_Exception('There is no file associated with the record');
		} // if
	} // function download

	/**
	* Prepares the search form
	*/
	public function action_search() {
		$this->load_model('search');

		if ( ! empty($_POST)) {
			// send the user back to page 1
			$this->page_offset = 1;
			// store the post (the search) in the session and the object
			$this->search = $this->controller_session['search'] = $_POST;

			$this->save_session();

			// redirect to the index page so the nav will work properly
			$this->redirect_to_index();

		} else {
			$this->template->page_title = 'Search - ' . $this->page_title_append;
			$view_title = $this->get_page_title_message('search');
			$view_content = $this->model->get_form(array(
				'mode' => 'search',
			));
			$this->add_default_view($view_title, $view_content);
		}
	} // function

	/**
	* Clears the search from the session and redirects the user to the index page for the model
	*/
	public function action_cancel_search() {
		// reset the search and search in the session
		$this->controller_session = $this->default_session;

		$this->save_session();

		$this->redirect_to_index();
	} // function action_cancel_search

	/**
	 * Exports the records, either all or checked using MultiORM.
	 * Generates either a PHPExcel file (if available) or CSV otherwise.
	 */
	public function action_export() {
		$this->load_model('add');

		// set up the admin options
		$options = array(
			'mode' => 'view',
			'sort_by_column' => $this->sort_column,
			'sort_by_order' => $this->sort_order,
			'in_search' => ( ! empty($this->search) || ! empty($this->sort_column)),
		);

		$orm_multiple = new MultiORM($this->model_name, $options);

		// there is a search so apply it
		if ( ! empty($this->search)) {
			$orm_multiple->set_search($this->search);
		}

		if ( ! Arr::get($_REQUEST, 'export_all', FALSE)) {
			$ids = (array) Arr::get($_REQUEST, 'ids', array());
			if ( ! empty($ids)) {
				$orm_multiple->set_ids($ids);
			}
		}

		$export_result = $orm_multiple->get_export();

		$output_name = URL::title($this->model_display_name) . '-' . date('YmdHis');

		// is an XLSX file generated by PHPExcel
		if (get_class($export_result) == 'PHPExcel') {
			$temp_xls_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cl4admin_export-' . Auth::instance()->get_user()->id . '-' . date('YmdHis') . '.xlsx';
			$output = PHPExcel_IOFactory::createWriter($export_result, 'Excel2007');
			$output->save($temp_xls_file);

			$this->response->send_file($temp_xls_file, $output_name . '.xlsx', array('delete' => TRUE));

		// is a CSV
		} else {
			$export_result->close_csv()
				->get_csv($this->response, $output_name . '.csv');
		}
	} // function action_export

	/**
	* Redirects the user to the index for the current model based on the current route
	*/
	function redirect_to_index() {
		$this->redirect(Route::get($this->route)->uri());
	} // function

	/**
	 * Saves the property "controller_session" to the session.
	 *
	 * @return void
	 */
	protected function save_session() {
		Session::instance()->set($this->session_key, $this->controller_session);
	}
} // class