<?php defined('SYSPATH') or die('No direct script access.');

class XM_Tree {
	public static function all_nodes($table_name, $expiry_col = TRUE) {
		$query = DB::select('node.*', array(DB::expr("COUNT(`parent`.`id`) - 1"), 'depth'))
			->from(array($table_name, 'node'), array($table_name, 'parent'))
			->where('node.lft', 'BETWEEN', array(DB::expr('`parent`.`lft`'), DB::expr('`parent`.`rgt`')))
			->group_by('node.id')
			->order_by('node.lft');

		if ($expiry_col) {
			$query->where_expiry('node')
				->where_expiry('parent');
		}

		return $query->execute();
	}

	public static function sub_nodes($table_name, $node_id, $expiry_col = TRUE) {
		$table_name = Database::instance()->quote_table($table_name);
		$node_id = Database::instance()->quote($node_id);

		return DB::query(Database::SELECT,
				"SELECT node.*, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth
				FROM {$table_name} AS node,
					{$table_name} AS parent,
					{$table_name} AS sub_parent,
					(
						SELECT node.*, (COUNT(parent.id) - 1) AS depth
						FROM {$table_name} AS node,
							{$table_name} AS parent
						WHERE node.lft BETWEEN parent.lft AND parent.rgt
							AND node.id = {$node_id}
							" . ($expiry_col ? "AND node.expiry_date = 0 AND parent.expiry_date = 0" : '') . "
						GROUP BY node.id
						ORDER BY node.lft
					) AS sub_tree
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
					AND sub_parent.id = sub_tree.id
					" . ($expiry_col ? "AND node.expiry_date = 0 AND parent.expiry_date = 0 AND sub_parent.expiry_date = 0" : '') . "
				GROUP BY node.id
				ORDER BY node.lft;")
			->execute();
	}

	public static function all_leafs($table_name, $expiry_col = TRUE) {
		$query = DB::select('node.*')
			->from(array($table_name, 'node'))
			->where('rgt', '=', DB::expr('lft + 1'));

		if ($expiry_col) {
			$query->where_expiry();
		}

		return $query->execute();
	}

	/**
	 * Get all the immediate sub nodes of a parent.
	 *
	 * @return Database_Result
	 */
	public static function immediate_nodes($table_name, $node_id = NULL, $expiry_col = TRUE) {
		if ($node_id === NULL) {
			$query = DB::select('id')
				->from($table_name)
				->where('lft', '=', 1);
			if ($expiry_col) {
				$query->where_expiry();
			}
			$node_id = $query->execute()
				->get('id');
		}

		$table_name = Database::instance()->quote_table($table_name);
		$node_id = Database::instance()->quote($node_id);

		return DB::query(Database::SELECT,
				"SELECT node.*, (COUNT(parent.id) - (sub_tree.depth + 1)) AS depth
				FROM {$table_name} AS node,
					{$table_name} AS parent,
					{$table_name} AS sub_parent,
					(
						SELECT node.*, (COUNT(parent.id) - 1) AS depth
						FROM {$table_name} AS node,
							{$table_name} AS parent
						WHERE node.lft BETWEEN parent.lft AND parent.rgt
							AND node.id = {$node_id}
							" . ($expiry_col ? "AND node.expiry_date = 0 AND parent.expiry_date = 0" : '') . "
						GROUP BY node.id
						ORDER BY node.lft
					) AS sub_tree
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
					AND sub_parent.id = sub_tree.id
					" . ($expiry_col ? "AND node.expiry_date = 0 AND parent.expiry_date = 0 AND sub_parent.expiry_date = 0" : '') . "
				GROUP BY node.id
				HAVING depth <= 1 AND depth > 0
				ORDER BY node.lft;")
			->execute();
	} // function immediate_nodes

	public static function parents($table_name, $node_id, $include_root = FALSE, $expiry_col = TRUE) {
		$query = DB::select('parent.*')
			->from(array($table_name, 'node'), array($table_name, 'parent'))
			->where('node.lft', 'BETWEEN', array(DB::expr('`parent`.`lft`'), DB::expr('`parent`.`rgt`')))
			->where('node.id', '=', $node_id)
			->order_by('node.lft');

		if ( ! $include_root) {
			$query->where('parent.lft', '>', 1);
		}
		if ($expiry_col) {
			$query->where_expiry('node')
				->where_expiry('parent');
		}

		return $query->execute();
	}

	public static function immediate_parent($table_name, $node_id, $expiry_col = TRUE) {
		$parents = Tree::parents($table_name, $node_id, FALSE, $expiry_col)
			->as_array();

		if (count($parents) > 1) {
			$rev_parents = array_reverse($parents);
			// 0 is the current node and 1 is the parent
			return $rev_parents[1];
		} else {
			return NULL;
		}
	}

	public static function lock_tables($table_name) {
		$table_name = Database::instance()->quote_table($table_name);

		return DB::query(NULL, "LOCK TABLES {$table_name} WRITE, `change_log` WRITE;")
			->execute();
	}

	public static function unlock_tables() {
		return DB::query(NULL, "UNLOCK TABLES;")
			->execute();
	}

	public static function add_node($node, $parent_id = NULL, $after_node_id = NULL, $expiry_col = TRUE) {
		$table_name = $node->table_name();

		Tree::lock_tables($table_name);

		if ($after_node_id !== NULL) {
			$query = DB::select(DB::expr("@myPos := rgt"))
				->from($table_name)
				->where('id', '=', $after_node_id);
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();

		// the first one below the parent
		} else {
			$query = DB::select(DB::expr("@myPos := lft"))
				->from($table_name)
				->where('id', '=', $parent_id);
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();
		}

		$query = DB::update($table_name)
			->set(array('rgt' => DB::expr('rgt + 2')))
			->where('rgt', '>', DB::expr('@myPos'));
		if ($expiry_col) {
			$query->where_expiry();
		}
		$query->execute();

		$query = DB::update($table_name)
			->set(array('lft' => DB::expr('lft + 2')))
			->where('lft', '>', DB::expr('@myPos'));
		if ($expiry_col) {
			$query->where_expiry();
		}
		$query->execute();

		// update the left and right on the node we added
		// do a "manual" query instead of using ORM because ORM seems to fail on some servers using the DB::expr()
		$query = DB::update($table_name)
			->set(array(
				'lft' => DB::expr('@myPos + 1'),
				'rgt' => DB::expr('@myPos + 2'),
			))
			->where('id', '=', $node->id);
		if ($expiry_col) {
			$query->where_expiry();
		}
		$query->execute();

		Tree::unlock_tables();
	} // function add_node

	public static function delete_node($node, $keep_children = FALSE, $expiry_col = TRUE) {
		$table_name = $node->table_name();

		Tree::lock_tables($table_name);

		$query = DB::select(DB::expr("@myLeft := lft"), DB::expr("@myRight := rgt"), DB::expr("@myWidth := rgt - lft + 1"))
			->from($table_name)
			->where('id', '=', $node->id);
		if ($expiry_col) {
			$query->where_expiry();
		}
		$query->execute();

		// don't keep the children, ie, delete all the children as well
		if ( ! $keep_children) {
			if ($expiry_col) {
				DB::update($table_name)
					->set(array('expiry_date' => DB::expr("NOW()")))
					->where('lft', 'BETWEEN', array(DB::expr("@myLeft"), DB::expr("@myRight")))
					->where_expiry()
					->execute();
			} else {
				DB::delete($table_name)
					->where('lft', 'BETWEEN', array(DB::expr("@myLeft"), DB::expr("@myRight")))
					->execute();
			}

			$query = DB::update($table_name)
				->set(array('rgt' => DB::expr("rgt - @myWidth")))
				->where('rgt', '>', DB::expr("@myRight"));
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();

			$query = DB::update($table_name)
				->set(array('lft' => DB::expr("lft - @myWidth")))
				->where('lft', '>', DB::expr("@myRight"));
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();

		// keep the children, ie, move the children up to the parent node
		} else {
			if ($expiry_col) {
				DB::update($table_name)
					->set(array('expiry_date' => DB::expr("NOW()")))
					->where('lft', '=', DB::expr('@myLeft'))
					->where_expiry()
					->execute();
			} else {
				DB::delete($table_name)
					->where('lft', '=', DB::expr('@myLeft'))
					->execute();
			}

			$query = DB::update($table_name)
				->set(array(
					'rgt' => DB::expr('rgt - 1'),
					'lft' => DB::expr('lft - 1'),
				))
				->where('lft', 'BETWEEN', array(DB::expr("@myLeft"), DB::expr("@myRight")));
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();

			$query = DB::update($table_name)
				->set(array('rgt' => DB::expr("rgt - 2")))
				->where('rgt', '>', DB::expr("@myRight"));
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();

			$query = DB::update($table_name)
				->set(array('lft' => DB::expr("lft - 2")))
				->where('lft', '>', DB::expr("@myRight"));
			if ($expiry_col) {
				$query->where_expiry();
			}
			$query->execute();
		} // if keep children

		Tree::unlock_tables();
	} // function delete_node

	public static function descendant_count($table_name, $node_id, $expiry_col = TRUE) {
		$query = DB::select('lft', 'rgt')
			->from($table_name)
			->where('id', '=', $node_id);

		if ($expiry_col) {
			$query->where_expiry();
		}

		$node = $query->execute()
			->current();

		return ($node['rgt'] - $node['lft'] - 1) / 2;
	}

	/**
	 *    Tree::convert('original_tree', 'new_tree');
	 *
	 * @return void
	 */
	public static function convert($orig_table_name, $result_table_name, $expiry_col = TRUE) {
		$query = DB::select()
			->from($result_table_name)
			->where('lft', '=', 1);
		if ($expiry_col) {
			$query->where_expiry();
		}
		$count = $query->execute()
			->count();
		if ($count === 0) {
			throw new Kohana_Exception('A root node must exist');
		}

		$query = DB::select()
			->from($orig_table_name)
			->where($orig_table_name . '.parent_id', '=', 0);
		if ($expiry_col) {
			$query->where_expiry($orig_table_name);
		}
		$top_levels = $query->execute();

		$last_node_id = NULL;
		foreach ($top_levels as $node) {
			$last_node_id = Tree::convert_node($orig_table_name, $result_table_name, $node, 1, $last_node_id, $expiry_col);
		}
	}

	public static function convert_node($orig_table_name, $result_table_name, $node, $parent_node_id, $last_node_id, $expiry_col = TRUE) {
		$_node = $node;
		if (isset($_node['id'])) {
			unset($_node['id']);
		}

		$new_node = ORM::factory($result_table_name)
			->values($_node)
			->save();

		Tree::add_node($new_node, $parent_node_id, $last_node_id);

		$query = DB::select()
			->from($orig_table_name)
			->where($orig_table_name . '.parent_id', '=', $node['parent_id']);
		if ($expiry_col) {
			$query->where_expiry($orig_table_name);
		}
		$top_levels = $query->execute();

		$last_node_id = NULL;
		foreach ($top_levels as $node) {
			$last_node_id = Tree::convert_node($orig_table_name, $result_table_name, $node, $new_node->id, $last_node_id, $expiry_col);
		}

		return $new_node->id;
	}
}