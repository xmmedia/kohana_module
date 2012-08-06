<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Tree extends Controller_Base {
	public $auth_required = TRUE;

	public $no_auto_render_actions = array('add');

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
			$add_at_root = Arr::get($_REQUEST, 'add_at_root', FALSE);

			if ( ! $add_at_root) {
				$parent_id = Arr::get($_REQUEST, 'parent_id', $this->request->param('id'));

				$parent_node = ORM::factory('tree', $parent_id);
			} else {
				$parent_node = ORM::factory('tree')
					->where('lft', '=', 1)
					->find();
			}

			if ( ! $parent_node->loaded()) {
				throw new Kohana_Exception('The parent ID was not received');
			}

			if ( ! empty($_POST)) {
				$new_node = ORM::factory('tree')
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

				DB::query(NULL, "LOCK TABLES `tree` WRITE, `change_log` WRITE;")
					->execute();

				if ($after_node_id !== NULL) {
					DB::select(DB::expr("@myPos := rgt"))
						->from('tree')
						->where('id', '=', $after_node_id)
						->execute();
				// the first one below the parent
				} else {
					DB::select(DB::expr("@myPos := lft"))
						->from('tree')
						->where('id', '=', $parent_id)
						->execute();
				}

				DB::update('tree')
					->set(array('rgt' => DB::expr('rgt + 2')))
					->where('rgt', '>', DB::expr('@myPos'))
					->execute();
				DB::update('tree')
					->set(array('lft' => DB::expr('lft + 2')))
					->where('lft', '>', DB::expr('@myPos'))
					->execute();
				$new_node->values(array(
						'lft' => DB::expr('@myPos + 1'),
						'rgt' => DB::expr('@myPos + 2'),
					))->save();

				DB::query(NULL, "UNLOCK TABLES;")
					->execute();

				Message::add('The new node <em>' . HTML::chars($new_node->name) . '</em> has been added.', Message::$notice);

				$this->request->redirect(Route::get('tree')->uri());
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
		}
	} // function action_add
}