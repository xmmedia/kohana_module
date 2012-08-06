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
}