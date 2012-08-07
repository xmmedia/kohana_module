<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Tree extends Controller_Base {
	public $auth_required = TRUE;

	public $no_auto_render_actions = array('add', 'edit', 'delete');

	public function before() {
		parent::before();

		$this->add_admin_css();

		if ($this->auto_render) {
			$this->template->styles['xm/css/tree.css'] = 'screen';
			$this->template->scripts['tree'] = 'xm/js/tree.js';
		}
	}

	/**
	 * Action: index
	 *
	 * @return void
	 */
	public function action_index() {
		try {
			// get a list of all the nodes with each one's depth
			$all_nodes = DB::select('node.id', 'node.name', 'node.lft', 'node.rgt', array(DB::expr("COUNT(`parent`.`id`) - 1"), 'depth'))
				->from(array('tree', 'node'), array('tree', 'parent'))
				->where('node.lft', 'BETWEEN', array(DB::expr('`parent`.`lft`'), DB::expr('`parent`.`rgt`')))
				->where_expiry('node')
				->where_expiry('parent')
				->group_by('node.id')
				->order_by('node.lft')
				->execute();

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

			$route = Route::get('tree');

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

				$tree_html .= '<li';
				if ($children_array[$node['id']]) {
					$tree_html .= ' class="has_children"><div><a href="" class="expand">';
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

			$root_node = ORM::factory('tree')
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

			$parent_node = ORM::factory('tree', $parent_id);
			if ( ! $parent_node->loaded()) {
				throw new Kohana_Exception('The parent ID was not received');
			}

			if ( ! empty($_POST)) {
				$new_node = ORM::factory('tree')
					->set_edit_fields()
					->save_values();

				$sibling_id = Arr::get($_REQUEST, 'sibling_id');
				if ( ! empty($sibling_id) && strtolower($sibling_id) != 'start') {
					$after_node_id = intval($sibling_id);
				} else if ($sibling_id == 'start') {
					$after_node_id = NULL;
				} else {
					// get all the immediate sub nodes of the parent we are adding to
					$_parent_subs = Tree::get_immediate_nodes($parent_node->id);

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

				Tree::lock_tables();

				if ($after_node_id !== NULL) {
					DB::select(DB::expr("@myPos := rgt"))
						->from('tree')
						->where('id', '=', $after_node_id)
						->where_expiry()
						->execute();
				// the first one below the parent
				} else {
					DB::select(DB::expr("@myPos := lft"))
						->from('tree')
						->where('id', '=', $parent_id)
						->where_expiry()
						->execute();
				}

				DB::update('tree')
					->set(array('rgt' => DB::expr('rgt + 2')))
					->where('rgt', '>', DB::expr('@myPos'))
					->where_expiry()
					->execute();
				DB::update('tree')
					->set(array('lft' => DB::expr('lft + 2')))
					->where('lft', '>', DB::expr('@myPos'))
					->where_expiry()
					->execute();
				$new_node->values(array(
						'lft' => DB::expr('@myPos + 1'),
						'rgt' => DB::expr('@myPos + 2'),
					))->save();

				Tree::unlock_tables();

				Message::add('The new node <em>' . HTML::chars($new_node->name) . '</em> has been added.', Message::$notice);
				$this->redirect();
			} // if post

			$tree_node = ORM::factory('tree')
				->set_mode('add');

			$_parent_subs = Tree::get_immediate_nodes($parent_node->id)
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

			$node = ORM::factory('tree', $node_id);
			if ( ! $node->loaded()) {
				throw new Kohana_Exception('The node could not be found');
			}

			$parent_node = Tree::immediate_parent($node->id);

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

			/*$_parent_subs = Tree::get_immediate_nodes($parent_node['id'])
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

			$node = ORM::factory('tree', $node_id);
			if ( ! $node->loaded()) {
				throw new Kohana_Exception('The node could not be found');
			}

			if ( ! empty($_POST)) {
				$keep_children = Arr::get($_REQUEST, 'keep_children', FALSE);

				Tree::lock_tables();

				DB::select(DB::expr("@myLeft := lft"), DB::expr("@myRight := rgt"), DB::expr("@myWidth := rgt - lft + 1"))
					->from('tree')
					->where('id', '=', $node->id)
					->where_expiry()
					->execute();

				// don't keep the children, ie, delete all the children as well
				if ( ! $keep_children) {
					DB::update('tree')
						->set(array('expiry_date' => DB::expr("NOW()")))
						->where('lft', 'BETWEEN', array(DB::expr("@myLeft"), DB::expr("@myRight")))
						->where_expiry()
						->execute();

					DB::update('tree')
						->set(array('rgt' => DB::expr("rgt - @myWidth")))
						->where('rgt', '>', DB::expr("@myRight"))
						->where_expiry()
						->execute();

					DB::update('tree')
						->set(array('lft' => DB::expr("lft - @myWidth")))
						->where('lft', '>', DB::expr("@myRight"))
						->where_expiry()
						->execute();

				// keep the children, ie, move the children up to the parent node
				} else {
					DB::update('tree')
						->set(array('expiry_date' => DB::expr("NOW()")))
						->where('lft', '=', DB::expr('@myLeft'))
						->where_expiry()
						->execute();

					DB::update('tree')
						->set(array(
							'rgt' => DB::expr('rgt - 1'),
							'lft' => DB::expr('lft - 1'),
						))
						->where('lft', 'BETWEEN', array(DB::expr("@myLeft"), DB::expr("@myRight")))
						->where_expiry()
						->execute();

					DB::update('tree')
						->set(array('rgt' => DB::expr("rgt - 2")))
						->where('rgt', '>', DB::expr("@myRight"))
						->where_expiry()
						->execute();

					DB::update('tree')
						->set(array('lft' => DB::expr("lft - 2")))
						->where('lft', '>', DB::expr("@myRight"))
						->where_expiry()
						->execute();
				} // if keep children

				Tree::unlock_tables();

				Message::add('<em>' . HTML::chars($node->name) . '</em> was deleted' . ($keep_children ? ' and it\'s children were kept' : '') . '.', Message::$notice);
				$this->redirect();
			} // if post

			$parent = Tree::immediate_parent($node->id);

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
		$this->request->redirect(Route::get('tree')->uri(array('action' => $action)) . $get);
	}
}