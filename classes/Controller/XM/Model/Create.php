<?php defined('SYSPATH') or die('No direct script access.');

/**
 * *** For programmer use ***
 * This controller handles the creation of models.
 */
class Controller_XM_Model_Create extends Controller_Private {
	public $page = 'xm_db_admin';

	public $secure_actions = array(
		'index' => 'xm_db_admin/model_create',
		'create' => 'xm_db_admin/model_create',
	);

	protected $no_auto_render_actions = array('create');

	/**
	 * Runs before the action
	 * Calls parent::before()
	 */
	public function before() {
		parent::before();

		$this->add_css();
	} // function before

	/**
	* Adds the CSS for xm_db_admin
	*/
	protected function add_css() {
		if ($this->auto_render) {
			$this->add_style('dbadmin', 'css/dbadmin.css')
				->add_script('model_create', 'xm/js/model_create.js');
		}
	} // function add_css

	/**
	 * Generates the page with a table list, some JS and a textarea for the generated PHP for a model
	 */
	public function action_index() {
		$db_group = XM::get_param('db_group', Database::$default);

		$table_list = Database::instance($db_group)->list_tables();
		$table_list = array_combine($table_list, $table_list);

		$db_list = array_keys((array) Kohana::$config->load('database'));
		$db_list = array_combine($db_list, $db_list);

		$this->template->body_html = View::factory('xm/model_create/index')
			->set('table_name', XM::get_param('table_name'))
			->set('db_group', $db_group)
			->bind('db_list', $db_list)
			->bind('table_list', $table_list);
	} // function action_index

	/**
	 * Runs ModelCreate::create_model(); adds what is returned to the the request->response and turns off auto render so we don't get the extra HTML from the template
	 */
	public function action_create() {
		$db_group = XM::get_param('db_group', Database::$default);

		// generate a base model file for the given table based on the database definition
		$model_create = new Model_Create($this->request->param('model'), array(
			'db_group' => $db_group,
		));

		AJAX_Status::echo_json(AJAX_Status::ajax(array(
			'model_code' => $model_create->create_model(),
		)));
	} // function action_create
} // class