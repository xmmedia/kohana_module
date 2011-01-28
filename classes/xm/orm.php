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
} // class ORM