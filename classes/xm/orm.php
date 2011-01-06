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

	protected function parent_save() {
		return Kohana_ORM::save();
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
	 * Also stores the record in _original incase a save is run later for single records.
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
				// store the database record in the original param
				$this->_original = $result->current();
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