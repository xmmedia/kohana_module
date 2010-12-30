<?php defined('SYSPATH') or die ('No direct script access.');

class XM_ORM extends cl4_ORM {
	/**
	* The default layout for get_field_layout()
	* @var  string
	*/
	public static $_default_layout;

	/**
	* Used in get_field_layout when the layout is table_row to keep track of the current row and add the row classes
	* @var  int
	*/
	protected $_current_table_row;

	/**
	*   Returns the HTML for displaying the field with a label tag, classes from object and errors, the field label and the actual field
	* This will recreate the label tag if the label class is passed
	*
	*   @param   string  $column_name  The field name of the field
	*   @param   string  $label_class  A custom class for the label for the field; if not set, the default in the class will be used
	*   @param   string  $layout_type  The type of layout needed for the field
	*       default (empty string): <label>Field Name</label> <field html />
	*       checkbox: <field html /><label>&nbsp;Field Name</label>
	*       table_row: <tr><td><label>Field Name</label></td><td><field html /></td></tr>
	*
	*   @return  string  The HTML for displaying the field
	*/
	public function get_field_layout($column_name, $label_class = NULL, $layout_type = NULL) {
		$regenerate_field = FALSE;

		// check to see if there is an error on the field
		// add a class to the field and the label
		if ($this->field_has_error($column_name)) {
			$regenerate_field = TRUE;
			$this->_table_columns[$column_name]['field_attributes'] = HTML::set_class_attribute($this->_table_columns[$column_name]['field_attributes'], 'cl4_field_error');
			$label_class = ( ! empty($label_class) ? $label_class . ' cl4_field_error' : 'cl4_field_error');
		}

		// prepare the field if it hasn't been prepared yet or if there has been a class added to the field because of an error
		if ( ! array_key_exists($column_name, $this->_field_html) || $regenerate_field) {
			$this->prepare_form($column_name);
		}

		// either regenerate the label or get it the one prepare_form() create
		if ( ! empty($label_class)) {
			$label_attributes = array();

			if ($this->_options['mode'] == 'edit' && isset($this->_rules[$column_name]['not_empty'])) {
				$label_attributes = HTML::set_class_attribute($label_attributes, 'cl4_required');
			}

			$label_attributes = HTML::set_class_attribute($label_attributes, $label_class);

			$label_html = Form::label($this->get_field_id($column_name), $this->get_field_label($column_name), $label_attributes);
		} else {
			$label_html = $this->_field_html[$column_name]['label'];
		}

		if ($layout_type === NULL) $layout_type = ORM::$_default_layout;

		switch ($layout_type) {
			case 'checkbox' :
				return $this->_field_html[$column_name]['field'] . $label_html . EOL;
				break;

			case 'table_row' :
				if ($this->_current_table_row === NULL) {
					$this->_current_table_row = 0;
				} else {
					++ $this->_current_table_row;
				}
				return '<tr class="row' . $this->_current_table_row . ' ' . ($this->_current_table_row % 2 ? 'odd' : 'even') . '">' . "\n\t" . '<td class="column0">' . $label_html . '</td>' . "\n\t" . '<td class="column1">' . $this->_field_html[$column_name]['field'] . '</td>' . EOL . '</tr>' . EOL;
				break;

			default :
				return $label_html . $this->_field_html[$column_name]['field'] . EOL;
				break;
        } // switch
	} // function get_field_layout

	public function field_has_error($column_name) {
		// check to see if there is an error on the field
		// add a class to the field and the label
		if ( ! empty($this->_validate)) {
			$errors = $this->_validate->errors();

			if (isset($errors[$column_name])) {
				return TRUE;
			}
		}

		return FALSE;
	}

	public function set_view_options_for_email() {
		$this->_options['checkmark_icons'] = FALSE;
		$this->_options['display_buttons'] = FALSE;
		$this->_options['table_options']['is_email'] = TRUE;

		return $this;
	}

    /**
	*
	*
	* @param mixed $column_name
	* @param mixed $attribute
	* @param mixed $value
	* @return ORM
	*/
	public function set_field_attribute($column_name, $attribute, $value = NULL) {
		if ($this->table_column_exists($column_name)) {
			$this->_table_columns[$column_name]['field_attributes'] = HTML::merge_attributes($this->_table_columns[$column_name]['field_attributes'], array($attribute => $value));
		}

		return $this;
	}

	/**
	*
	*
	* @param mixed $column_name
	* @param mixed $option
	* @param mixed $value
	* @return ORM
	*/
	public function set_field_option($column_name, $option, $value = NULL) {
		if ($this->table_column_exists($column_name)) {
			$this->_table_columns[$column_name]['field_options'][$option] = $value;
		}

		return $this;
	}

	protected function table_column_exists($column_name) {
		return array_key_exists($column_name, $this->_table_columns);
	}

    	/**
	* @return ORM
	*/
	public function only_active() {
		$this->_db_pending[] = array(
			'name' => 'where',
			'args' => array($this->_table_name . '.active_flag', '=', 1),
		);

		return $this;
	}

	/**
	*
	* @return ORM
	*/
	public function set_display_order() {
		$this->order_by($this->_table_name . '.display_order', 'ASC')
			->order_by($this->_table_name . '.id', 'ASC');

		return $this;
	}

	/**
	*
	*
	* @param mixed $column_name
	* @param mixed $id
	* @return ORM
	*/
	public function set_field_id($column_name, $id = NULL) {
		return $this->set_field_attribute($column_name, 'id', $id);
	}

	/**
	*
	*
	* @param mixed $column_name
	* @param mixed $field_type
	* @return ORM
	*/
	public function set_field_type($column_name, $field_type) {
		if ($this->table_column_exists($column_name)) {
			$this->_table_columns[$column_name]['field_type'] = $field_type;
		}

		return $this;
	}

	/**
	*
	*
	* @param mixed $prefix
	* @return ORM
	*/
	public function set_field_name_prefix($prefix = 'c_record') {
		$this->_options['field_name_prefix'] = $prefix;

		return $this;
	}



	// contains the original record, populate during find()
	protected $_original = array();
	// no save run: NULL
	// no save needed: FALSE
	// save run: TRUE
	protected $_was_updated;
	// by default, log any changes
	protected $_log = TRUE;
	public $_was_insert;
	public $_was_update;

	/**
	 * Finds and loads a single database row into the object.
	 * Also stores the record in _original incase a save is run later
	 *
	 * @chainable
	 * @param   mixed  primary key
	 * @return  ORM
	 */
	public function find($id = NULL) {
		$find_return = parent::find($id);

		// store the original record
		$this->_original = $this->_object;

		return $find_return;
	}

	/**
	 * Saves the current object.
	 * Checks to see if an columns have actually changed values before saving
	 *
	 * @chainable
	 * @return  ORM
	 *
	 * @todo  add flag to turn of checking for changes
	 */
	public function save() {
		$this->_was_updated = FALSE;

		// make sure the record is loaded, if it can be
		$this->loaded();

		// will contain an array of the fields and the new values
		$changed = array();

		// is update?
		if ( ! $this->empty_pk()) {
			// loop through the changed array comparing it to the original array to check for changed fields
			foreach ($this->_changed as $column) {
				// determine if the column has changed
				if ($this->column_changed($column)) {
					// value has changed
					$changed[$column] = $this->_object[$column];
				}
			} // foreach

		// not update, so must be insert
		} else {
			// everything is considered new/changed
			foreach ($this->_changed as $column) {
				$changed[$column] = $this->_object[$column];
			}
		}

		// have there been fields changed?
		if ( ! empty($changed)) {
			// yes, so run save functionality
			if ( ! $this->empty_pk() AND ! isset($this->_changed[$this->_primary_key])) {
				// Primary key isn't empty and hasn't been changed so do an update
				$query_type = 'UPDATE';
				$this->_was_insert = FALSE;
				$this->_was_update = TRUE;

				if (is_array($this->_updated_column)) {
					// Fill the updated column
					$column = $this->_updated_column['column'];
					$format = $this->_updated_column['format'];

					$data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
				}

				$query = DB::update($this->_table_name)
					->set($changed)
					->where($this->_primary_key, '=', $this->pk())
					->execute($this->_db);

				// Object has been saved
				$this->_saved = TRUE;

			} else {
				// primary key isn't set and hasn't changed, so insert
				$query_type = 'INSERT';
				$this->_was_insert = TRUE;
				$this->_was_update = FALSE;

				if (is_array($this->_created_column)) {
					// Fill the created column
					$column = $this->_created_column['column'];
					$format = $this->_created_column['format'];

					$data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
				}

				$result = DB::insert($this->_table_name)
					->columns(array_keys($changed))
					->values(array_values($changed))
					->execute($this->_db);

				if ($result) {
					if ($this->empty_pk()) {
						// Load the insert id as the primary key
						// $result is array(insert_id, total_rows)
						$this->_object[$this->_primary_key] = $result[0];
					}

					// Object is now loaded and saved
					$this->_loaded = $this->_saved = TRUE;
				}
			}

			if ($this->_saved === TRUE) {
				// All changes have been saved
				$this->_changed = array();
				$this->_was_updated = TRUE;
			}

			if ($this->_log) {
				$change_log = ORM::factory('change_log')
					->add_change_log(array(
						'table_name' => $this->_table_name,
						// send the original pk so the change to the pk can be tracked when doing an update
						'record_pk' => ($query_type == 'UPDATE' ? $this->_original[$this->_primary_key] : $this->pk()),
						'query_type' => $query_type,
						'row_count' => ($query_type == 'UPDATE' ? $query : $result[1]), // @todo determine if it's always 1 or if there's a way to determine how many
						'query_time' => $this->_db->last_query_time,
						'sql' => $this->_db->last_query,
						'changed' => $changed,
					));
			} // if log

		// no there have not been records saved, but still set the object as saved and empty the changed array because it's exactly the same as what's in the DB
		} else {
			$this->_saved = TRUE;

			// All changes have been saved
			$this->_changed = array();

			// since changes as empty, we can assume that we didn't add a new record and it must have been a existing record that didn't need an update
			$this->_was_insert = FALSE;
			$this->_was_update = TRUE;
		}
/*
		if ($this->_saved) {
			// check for has_many relationships and associated changes (adds / deletes)
			// todo: should we add some of this to check() ?
			foreach ($this->_has_many as $foreign_object => $relation_data) {
				// can't process this table because the through table is not set
				if (empty($relation_data['through'])) continue;

				$through_table = $relation_data['through'];
				$source_model = ! empty($relation_data['source_model']) ? $relation_data['source_model'] : NULL; // todo: should not need 'source_model' since we have the relationship via the model

				// get the current associated record ids
				$current = $this->get_foreign_values($through_table, $relation_data);
				//echo '<p>old records: ' . kohana::debug($current) . '</p>';

				// see if there are any valid records to add and add them and keep track of which current ones are not re-added
				if (isset($_POST[$through_table]) && is_array($_POST[$through_table])) {
					//echo '<p>new records: ' . kohana::debug($_POST[$through_table]) . '</p>';
					if (isset($_POST[$through_table][0])) unset($_POST[$through_table][0]);
					foreach ($_POST[$through_table] AS $record_id) {
						// if the record is not already set, set it
						if ( ! in_array($record_id, $current) ) {
							try {
								DB::insert($through_table, array($relation_data['foreign_key'], $relation_data['far_key']))
									->values(array($this->pk(), $record_id))
									->execute($this->_db);
								//echo '<p>add record id: ' . $record_id . '</p>';
							} catch (Exception $e) {
								throw $e;
							}
						} else {
							unset($current[$record_id]); // remove from current list
						} // if
					} // foreach

					// remove any current ones that were not re-added, $current should now have existing entries that were not re-added
					foreach ($current AS $record_id) {
						try {
							DB::delete($through_table)
								->where($relation_data['foreign_key'],'=',$this->pk())
								->where($relation_data['far_key'],'=',$record_id)
								->execute($this->_db);
							//echo '<p>add record id: ' . $record_id . '</p>';
							if (in_array($record_id, $current)) unset($current[$record_id]); // remove from current list
						} catch (Exception $e) {
							throw $e;
						}
						//echo '<p>remove record id: ' . $record_id . '</p>';
					} // foreach

				} else {
					// does not appear to be any associated form fields in the $_POST
					// maybe the form was not created with get_form()
					// ok, just proceed

				} // if
			} // foreach
		} // if
*/
		return $this;
	} // function save

	protected function parent_save() {
		return Kohana_ORM::save();
	}

	/**
	*
	* @return  bool  true if the value has changed
	*/
	protected function column_changed($column) {
		// if the column does not existing in the original record
		$changed = ( ! array_key_exists($column, $this->_original)
			// or the original value is NULL and the new value is NULL
			|| ($this->_original[$column] === NULL && $this->_object[$column] !== NULL)
			// or the value does not match the original
			|| $this->_original[$column] != $this->_object[$column]);

		if ($changed && $this->table_column_exists($column)) {
			switch ($this->_table_columns[$column]['field_type']) {
				case 'phone' :
					// original was empty (never set) and the value in the object the dashes returned by ORM_Phone
					if ($this->_original[$column] == '' && $this->_object[$column] == '----') {
						$changed = FALSE;
					}
					break;
				case 'date' :
					// the original value has not date and user did not submit a date
					if ($this->_original[$column] == '0000-00-00' && $this->_object[$column] == '') {
						$changed = FALSE;
					}
					break;
			} // switch
		} // if

		return $changed;
	}

	/**
	* Returns a string of the values in the current object
	*
	* @param  string  $columns   The column to include in the concat; If more than 1 column is wanted, pass a string including CONCAT() or similar; If not column is passed, the primary value in the model will be used
	* @param  array   $order_by  The sorting to use; pass an array the same way the sorting key in the model is set; if nothing passed and no _sorting property, no ordering will be applied
	* @return  string  Comma separated list of values (as generated by MySQL)
	*/
	public function group_concat($columns = NULL, $order_by = NULL) {
		if (empty($columns)) {
			$columns = Database::instance()->quote_identifier($this->_table_name . '.' . $this->_primary_val);
		}

		if (empty($order_by)) {
			$order_by = array($this->_sorting);
		}
		if ( ! empty($order_by)) {
			$sort = array();
			foreach ($this->_sorting as $column => $direction) {
				if ( ! empty($direction)) {
					// Make the direction uppercase
					$direction = ' ' . strtoupper($direction);
				}

				if (strpos($column, '.') === FALSE) {
					$column = $this->_table_name . '.' . $column;
				}

				$sort[] = Database::instance()->quote_identifier($column) . $direction;
			}

			$order_by = 'ORDER BY '.implode(', ', $sort);
		} else {
			$order_by = '';
		}

		$query = $this->select(DB::expr("GROUP_CONCAT(DISTINCT {$columns} {$order_by} SEPARATOR ', ') AS group_concat"))
			->find();

		return $query->group_concat;
	} // function group_concat

	/**
	 * Loads a database result, either as a new object for this model, or as
	 * an iterator for multiple rows.
	 * Same as Kohana_ORM::_load_result but checks to see if there is a current select and if there is, doesn't add the select *
	 *
	 * @chainable
	 * @param   boolean       return an iterator or load a single row
	 * @return  ORM           for single rows
	 * @return  ORM_Iterator  for multiple rows
	 */
	protected function _load_result($multiple = FALSE) {
		$this->_db_builder->from($this->_table_name);

		if ($multiple === FALSE) {
			// Only fetch 1 record
			$this->_db_builder->limit(1);
		}

		if ( ! isset($this->_db_applied['select'])) {
			// Select all columns by default
			$this->_db_builder->select($this->_table_name.'.*');
		}

		if ( ! isset($this->_db_applied['order_by']) AND ! empty($this->_sorting)) {
			foreach ($this->_sorting as $column => $direction) {
				if (strpos($column, '.') === FALSE) {
					// Sorting column for use in JOINs
					$column = $this->_table_name.'.'.$column;
				}

				$this->_db_builder->order_by($column, $direction);
			}
		}

		if ($multiple === TRUE) {
			// Return database iterator casting to this object type
			$result = $this->_db_builder->as_object(get_class($this))->execute($this->_db);

			$this->reset();

			return $result;
		} else {
			// Load the result as an associative array
			$result = $this->_db_builder->as_assoc()->execute($this->_db);

			$this->reset();

			if ($result->count() === 1) {
				// Load object values
				$this->_load_values($result->current());
			} else {
				// Clear the object, nothing was found
				$this->clear();
			}

			return $this;
		}
	}
} // class ORM