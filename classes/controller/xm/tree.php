<?php defined('SYSPATH') or die('No direct script access.');

class Controller_XM_Tree extends Controller_Base {
	public $auth_required = TRUE;

	public function before() {
		parent::before();

		$this->add_admin_css();

		$this->template->styles['xm/css/tree.css'] = 'screen';
		$this->template->scripts['tree'] = 'xm/js/tree.js';
	}

	/**
	 * Action: index
	 *
	 * @return void
	 */
	public function action_index() {
		try {
			// get a list of all the nodes with each one's depth
			$all_nodes = DB::select('node.id', 'node.name', array(DB::expr("COUNT(`parent`.`name`) - 1"), 'depth'))
				->from(array('tree', 'node'), array('tree', 'parent'))
				->where('node.lft', 'BETWEEN', array(DB::expr('`parent`.`lft`'), DB::expr('`parent`.`rgt`')))
				->where_expiry('node')
				->where_expiry('parent')
				->group_by('node.name')
				->order_by('node.lft')
				->execute();

			// determine which nodes have children
			$children_array = array();
			$current_depth = 0;
			$last_node_id = NULL;
			foreach ($all_nodes as $node) {
				$node_depth = $node['depth'];

				if ($node_depth == 0 || $node_depth > $current_depth) {
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

			// create the tree
			$current_depth = 0;
			$counter = 0;
			$tree_html = '<ul class="tree">';
			foreach($all_nodes as $node){
				$node_depth = $node['depth'];

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
						. '<a href="" class="cl4_edit edit_item" title="Edit Item"></a>'
						. '<a href="" class="cl4_delete delete_item" title="Delete Item"></a>'
						. '<a href="" class="cl4_add add_sub_item" title="Add Sub Item"></a>'
					. '</div>'
				. '</div>';

				++ $counter;
			} // foreach
			$tree_html .= str_repeat('</li></ul>', $node_depth) . '</li>'
				. '</ul>';

			$this->template->body_html = View::factory('tree/index')
				->bind('tree_html', $tree_html);
		} catch (Exception $e) {
			Kohana_Exception::caught_handler($e);
		}
	} // function action_index
}