<?php defined('SYSPATH') or die('No direct script access.');

class XM_Tree {
	/**
	 * Get all the immediate sub nodes of the parent we are adding to.
	 *
	 * @return Database_Result
	 */
	public static function get_immediate_nodes($node_id = NULL) {
		if ($node_id === NULL) {
			$node_id = ORM::factory('tree')
				->where('lft', '=', 1)
				->find()
				->id;
		}

		$node_id = Database::instance()->quote($node_id);

		return DB::query(Database::SELECT,
				"SELECT node.*, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth
				FROM tree AS node,
					tree AS parent,
					tree AS sub_parent,
					(
						SELECT node.*, (COUNT(parent.id) - 1) AS depth
						FROM tree AS node,
							tree AS parent
						WHERE node.lft BETWEEN parent.lft AND parent.rgt
							AND node.id = {$node_id}
						GROUP BY node.id
						ORDER BY node.lft
					) AS sub_tree
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
					AND sub_parent.id = sub_tree.id
				GROUP BY node.id
				HAVING depth <= 1 AND depth > 0
				ORDER BY node.lft;")
			->execute();
	} // function get_immediate_nodes

	public static function parents($node_id, $include_root = FALSE) {
		$query = DB::select('parent.*')
			->from(array('tree', 'node'), array('tree', 'parent'))
			->where('node.lft', 'BETWEEN', array(DB::expr('`parent`.`lft`'), DB::expr('`parent`.`rgt`')))
			->where('node.id', '=', $node_id)
			->order_by('node.lft');
		if ( ! $include_root) {
			$query->where('parent.lft', '>', 1);
		}
		return $query->execute();
	}

	public static function immediate_parent($node_id) {
		$parents = Tree::parents($node_id)
			->as_array();

		if (count($parents) > 1) {
			$rev_parents = array_reverse($parents);
			// 0 is the current node and 1 is the parent
			return $rev_parents[1];
		} else {
			return NULL;
		}
	}

	public static function lock_tables() {
		return DB::query(NULL, "LOCK TABLES `tree` WRITE, `change_log` WRITE;")
			->execute();
	}

	public static function unlock_tables() {
		return DB::query(NULL, "UNLOCK TABLES;")
			->execute();
	}
}