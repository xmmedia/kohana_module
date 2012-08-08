<?php defined('SYSPATH') or die('No direct script access.');

/**
 *    Route::set('tree', 'tree(/<action>(/<id>))')
 *        ->defaults(array(
 *            'controller' => 'tree',
 *    ));
 *
 */
class Controller_XM_Tree extends Controller_Base {
	public $auth_required = TRUE;

	public $no_auto_render_actions = array('add', 'edit', 'delete');

	protected $model_name = 'tree';
	protected $route_name = 'tree';

	// set in the before based on the table name in the mode
	protected $table_name;

	public function before() {
		parent::before();

		$this->add_admin_css();

		if ($this->auto_render) {
			$this->template->styles['xm/css/tree.css'] = 'screen';
			$this->template->scripts['jstorage'] = 'xm/js/jstorage.min.js';
			$this->template->scripts['tree'] = 'xm/js/tree.js';
		}

		$temp_model = ORM::factory($this->model_name);
		$this->table_name = $temp_model->table_name();
	}

	/**
	 * Action: index
	 *
	 * @return void
	 */
	public function action_index() {
		try {
			// get a list of all the nodes with each one's depth
			$all_nodes = Tree::all_nodes($this->table_name);

			// determine which nodes have children
			$children_array = array();
			$current_depth = 0;
			$last_node_id = NULL;
			foreach ($all_nodes as $node) {
				$node_depth = $node['depth'];
				// skip the "root" node
				if ($node_depth == 0) {
					continue;
				}

				if ($node_depth > $current_depth) {
					$children_array[$last_node_id] = TRUE;
				}

				if ($node_depth > $current_depth) {
					$current_depth = $current_depth + ($node_depth - $current_depth);
				} else if ($node_depth < $current_depth) {
					$current_depth = $current_depth - ($current_depth - $node_depth);
				}

				$children_array[$node['id']] = FALSE;

				$last_node_id = $node['id'];
			} // foreach

			$route = Route::get($this->route_name);

			// create the tree
			$current_depth = 0;
			$counter = 0;
			$tree_html = '<ul class="tree">';
			foreach($all_nodes as $node){
				$node_depth = $node['depth'];
				// skip the "root" node
				if ($node_depth == 0) {
					continue;
				}

				if ($node_depth == $current_depth) {
					if ($counter > 0) $tree_html .= '</li>';

				} else if ($node_depth > $current_depth) {
					$tree_html .= '<ul>';
					$current_depth = $current_depth + ($node_depth - $current_depth);

				} else if ($node_depth < $current_depth) {
					$tree_html .= str_repeat('</li></ul>', $current_depth - $node_depth) . '</li>';
					$current_depth = $current_depth - ($current_depth - $node_depth);
				}

				$tree_html .= '<li rel="' . $node['id'] . '"';
				if ($children_array[$node['id']]) {
					$tree_html .= ' class="has_children"><div><a href="" class="expand" rel="' . $node['id'] . '">';
				} else {
					$tree_html .= '><div><a href="" class="no_expand">';
				}
				$tree_html .= '</a><div class="name">' . HTML::chars($node['name']) . '</div>'
					. '<div class="links">'
						. HTML::anchor($route->uri(array('action' => 'edit', 'id' => $node['id'])) . '?c_ajax=1', '', array('class' => 'cl4_edit edit_item', 'title' => 'Edit Item'))
						. HTML::anchor($route->uri(array('action' => 'add', 'id' => $node['id'])) . '?c_ajax=1', '', array('class' => 'cl4_add add_sub_item', 'title' => 'Add Sub Item'))
						. HTML::anchor($route->uri(array('action' => 'delete', 'id' => $node['id'])) . '?c_ajax=1', '', array('class' => 'cl4_delete delete_item', 'title' => 'Delete Item'))
					. '</div>'
				. '</div>';

				++ $counter;
			} // foreach
			$tree_html .= str_repeat('</li></ul>', $node_depth) . '</li>'
				. '</ul>';

			$root_node = ORM::factory($this->model_name)
				->where('lft', '=', 1)
				->find();

			$this->template->body_html = View::factory('tree/index')
				->bind('tree_html', $tree_html)
				->bind('root_node', $root_node);

		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
			Message::add('There was a problem preparing the tree.', Message::$error);
		}
	} // function action_index

	/**
	 * Action: add
	 *
	 * @return void
	 */
	public function action_add() {
		try {
			$is_ajax = (bool) Arr::get($_REQUEST, 'c_ajax', FALSE);
			$parent_id = Arr::get($_REQUEST, 'parent_id', $this->request->param('id'));

			$parent_node = ORM::factory($this->model_name, $parent_id);
			if ( ! $parent_node->loaded()) {
				throw new Kohana_Exception('The parent ID was not received');
			}

			if ( ! empty($_POST)) {
				$new_node = ORM::factory($this->model_name)
					->set_edit_fields()
					->save_values();

				$sibling_id = Arr::get($_REQUEST, 'sibling_id');
				if ( ! empty($sibling_id) && strtolower($sibling_id) != 'start') {
					$after_node_id = intval($sibling_id);
				} else if ($sibling_id == 'start') {
					$after_node_id = NULL;
				} else {
					// get all the immediate sub nodes of the parent we are adding to
					$_parent_subs = Tree::immediate_nodes($this->table_name, $parent_node->id);

					// create array of the parent's subs using the id and name as the key
					// this will ensure we get a unique key
					$parent_subs = array();
					foreach ($_parent_subs as $_parent_sub) {
						$parent_subs[$_parent_sub['name'] . '-' . $_parent_sub['id']] = $_parent_sub['id'];
					}
					$parent_subs[$new_node->name] = NULL;
					ksort($parent_subs);

					$after_node_id = NULL;
					foreach ($parent_subs as $parent_sub_name => $parent_sub_id) {
						if ($parent_sub_name == $new_node->name) {
							break;
						}
						$after_node_id = $parent_sub_id;
					}
				} // if

				Tree::add_node($new_node, $parent_id, $after_node_id);

				Message::add('The new node <em>' . HTML::chars($new_node->name) . '</em> has been added.', Message::$notice);
				$this->redirect();
			} // if post

			$tree_node = ORM::factory($this->model_name)
				->set_mode('add');

			$_parent_subs = Tree::immediate_nodes($this->table_name, $parent_node->id)
				->as_array('id', 'name');

			$add_values = array(
				'' => '-- Automatic (Alphabetically) --',
				'start' => '-- At the Beginning --',
			);
			$sibling_select = Form::select('sibling_id', $_parent_subs, NULL, array('id' => 'sibling_id'), array('add_values' => $add_values));

			AJAX_Status::echo_json(AJAX_Status::ajax(array(
				'html' => (string) View::factory('tree/add')
					->bind('parent_node', $parent_node)
					->bind('tree_node', $tree_node)
					->bind('sibling_select', $sibling_select),
			)));

		} catch (Exception $e) {
			$msg = 'There was a problem adding a node or loading the add node.';
			$this->exception($e, $msg, $is_ajax);
		}
	} // function action_add

	/**
	 * Action: edit
	 *
	 * @return void
	 */
	public function action_edit() {
		try {
			$is_ajax = (bool) Arr::get($_REQUEST, 'c_ajax', FALSE);
			$node_id = $this->request->param('id');

			$node = ORM::factory($this->model_name, $node_id);
			if ( ! $node->loaded()) {
				throw new Kohana_Exception('The node could not be found');
			}

			$parent_node = Tree::immediate_parent($this->table_name, $node->id);

			if ( ! empty($_POST)) {
				/*$sibling_id = Arr::get($_REQUEST, 'sibling_id');
				// move the node to somewhere else on it's current branch
				if ( ! empty($sibling_id) && strtolower($sibling_id) != 'start') {
					$after_node_id = intval($sibling_id);
				} else if ($sibling_id == 'start') {
					$after_node_id = NULL;
				}*/

				$node->set_edit_fields()
					->save_values()
					->save();

				Message::add('The node was saved successfully.', Message::$notice);
				$this->redirect();
			}

			/*$_parent_subs = Tree::immediate_nodes($this->table_name, $parent_node['id'])
				->as_array('id', 'name');
			if (isset($_parent_subs[$node->id])) {
				unset($_parent_subs[$node->id]);
			}

			$add_values = array(
				'' => '-- Leave Where It Is --',
				'start' => '-- Move to the Beginning --',
			);
			$sibling_select = Form::select('sibling_id', $_parent_subs, NULL, array('id' => 'sibling_id'), array('add_values' => $add_values));*/

			AJAX_Status::echo_json(AJAX_Status::ajax(array(
				'html' => (string) View::factory('tree/edit')
					->bind('node', $node)
					->bind('sibling_select', $sibling_select),
			)));

		} catch (Exception $e) {
			$msg = 'There was a problem edit a node or loading the edit node.';
			$this->exception($e, $msg, $is_ajax);
		}
	} // function action_edit

	/**
	 * Action: delete
	 *
	 * @return void
	 */
	public function action_delete() {
		try {
			$is_ajax = (bool) Arr::get($_REQUEST, 'c_ajax', FALSE);
			$node_id = $this->request->param('id');

			$node = ORM::factory($this->model_name, $node_id);
			if ( ! $node->loaded()) {
				throw new Kohana_Exception('The node could not be found');
			}

			if ( ! empty($_POST)) {
				$keep_children = Arr::get($_REQUEST, 'keep_children', FALSE);

				Tree::delete_node($node, $keep_children);

				Message::add('<em>' . HTML::chars($node->name) . '</em> was deleted' . ($keep_children ? ' and it\'s children were kept' : '') . '.', Message::$notice);
				$this->redirect();
			} // if post

			$parent = Tree::immediate_parent($this->table_name, $node->id);

			AJAX_Status::echo_json(AJAX_Status::ajax(array(
				'html' => (string) View::factory('tree/delete')
					->bind('parent', $parent)
					->bind('node', $node),
			)));

		} catch (Exception $e) {
			$msg = 'There was a problem deleting a node or loading the delete confirmation.';
			$this->exception($e, $msg, $is_ajax);
		}
	} // function action_delete

	protected function exception($e, $msg, $is_ajax = FALSE) {
		if ($is_ajax) {
			Kohana_Exception::caught_handler($e, FALSE, FALSE);

			$ajax_data = array(
				'status' => AJAX_Status::UNKNOWN_ERROR,
				'error_msg' => $msg,
				'html' => $msg,
				'debug_msg' => Kohana_Exception::text($e),
			);
			AJAX_Status::echo_json(AJAX_Status::ajax($ajax_data));
		} else {
			Kohana_Exception::caught_handler($e);
			Message::add($msg, Message::$error);
		}
	} // function exception

	/**
	 * Redirects the user based to the action on the tree route.
	 *
	 * @param  string  $action  The action to redirect to. Use NULL for index or default.
	 * @param  string  $get     Any additional get parameter to add.
	 * @return void
	 */
	protected function redirect($action = NULL, $get = '') {
		$this->request->redirect(Route::get($this->route_name)->uri(array('action' => $action)) . $get);
	}
}