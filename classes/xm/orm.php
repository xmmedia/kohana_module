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
	*   @param   array   $options      Array of options
	*       label_attributes => attributes to be added to the label, if this isn't an empty array then the label generated by prepare_form() won't be used
	*
	*   @return  string  The HTML for displaying the field
	*/
	public function get_field_layout($column_name, $label_class = NULL, $layout_type = NULL, array $options = array()) {
		$regenerate_field = FALSE;

		$options += array(
			'label_attributes' => array(),
			'custom_view' => NULL,
		);

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
		if ( ! empty($label_class) || ! empty($options['label_attributes'])) {
			$label_attributes = $options['label_attributes'];

			$label_attributes = HTML::set_class_attribute($label_attributes, $label_class);

			$rules = $this->rules();
			if ($this->_options['mode'] == 'edit' && isset($rules[$column_name]['not_empty'])) {
				$label_attributes = HTML::set_class_attribute($label_attributes, 'cl4_required');
			}

			$label_html = Form::label($this->get_field_id($column_name), $this->column_label($column_name), $label_attributes);
		} else {
			$label_html = $this->_field_html[$column_name]['label'];
		}

		if ($layout_type === NULL) $layout_type = ORM::$_default_layout;

		switch ($layout_type) {
			case 'checkbox' :
				return $this->_field_html[$column_name]['field'] . ' ' . $label_html . $this->_field_html[$column_name]['help'] . EOL;
				break;

			case 'radio_above' :
				return $label_html . '<br>' . $this->_field_html[$column_name]['field'] . '<br>' . $this->_field_html[$column_name]['help'] . EOL;
				break;

			case 'table_row' :
				if ($this->_current_table_row === NULL) {
					$this->_current_table_row = 0;
				} else {
					++ $this->_current_table_row;
				}
				return '<tr class="row' . $this->_current_table_row . ' ' . ($this->_current_table_row % 2 ? 'odd' : 'even') . '">' . "\n\t" . '<td class="column0">' . $label_html . '</td>' . "\n\t" . '<td class="column1">' . $this->_field_html[$column_name]['field'] . '</td>' . EOL . '</tr>' . EOL;
				break;

			case 'custom' :
				return View::factory($options['custom_view'])
					->bind('model', $this)
					->bind('column_name', $column_name)
					->bind('field_html', $this->_field_html)
					->bind('current_table_row', $this->_current_table_row)
					->bind('label_html', $label_html);
				break;

			default :
				return $label_html . $this->_field_html[$column_name]['field'] . EOL;
				break;
		} // switch
	} // function get_field_layout

	/**
	 * Outputs the field label.
	 *
	 *   @param   string  $column_name  The field name of the field
	 *   @param   string  $label_class  A custom class for the label for the field; if not set, the default in the class will be used
	 *   @param   array   $options      Array of options
	 *       label_attributes => attributes to be added to the label, if this isn't an empty array then the label generated by prepare_form() won't be used
	 *
	 *   @return  string  The HTML for displaying the label
	 */
	public function get_field_label($column_name, $label_class = NULL, array $options = array()) {
		$regenerate_field = FALSE;

		$options += array(
			'label_attributes' => array(),
		);

		// check to see if there is an error on the field
		// add a class to the field and the label
		if ($this->field_has_error($column_name)) {
			$regenerate_field = TRUE;
			$label_class = ( ! empty($label_class) ? $label_class . ' cl4_field_error' : 'cl4_field_error');
		}

		// prepare the field if it hasn't been prepared yet or if there has been a class added to the field because of an error
		if ( ! array_key_exists($column_name, $this->_field_html) || $regenerate_field) {
			$this->prepare_form($column_name);
		}

		// either regenerate the label or get it the one prepare_form() create
		if ( ! empty($label_class) || ! empty($options['label_attributes'])) {
			$label_attributes = $options['label_attributes'];

			$label_attributes = HTML::set_class_attribute($label_attributes, $label_class);

			$rules = $this->rules();
			if ($this->_options['mode'] == 'edit' && isset($rules[$column_name]['not_empty'])) {
				$label_attributes = HTML::set_class_attribute($label_attributes, 'cl4_required');
			}

			$label_html = Form::label($this->get_field_id($column_name), $this->column_label($column_name), $label_attributes);
		} else {
			$label_html = $this->_field_html[$column_name]['label'];
		}

		return $label_html;
	} // function get_field_label

	public function field_has_error($column_name) {
		// check to see if there is an error on the field
		// add a class to the field and the label
		if ( ! empty($this->_validation)) {
			$errors = $this->_validation->errors();

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

	protected function parent_save() {
		return Kohana_ORM::save();
	}

    	/**
	* @return ORM
	*/
	public function only_active() {
		$this->_db_pending[] = array(
			'name' => 'where',
			'args' => array($this->_object_name . '.active_flag', '=', 1),
		);

		return $this;
	}

	/**
	*
	* @return ORM
	*/
	public function set_display_order() {
		$this->order_by($this->_object_name . '.display_order', 'ASC')
			->order_by($this->_object_name . '.id', 'ASC');

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
	 * Retrieves the value of a radio based on the source data in the _table_columns source array.
	 * Will also work for other field types that have the source data in the _table_columns array.
	 * Will return NULL if the field doesn't exist, the source data or value doesn't exist.
	 *
	 * @param  string  $column_name  The column name to retrieve.
	 * @return  string
	 */
	public function get_radio_value_string($column_name) {
		if ($this->table_column_exists($column_name) && isset($this->_table_columns[$column_name]['field_options']['source']['data'][$this->$column_name])) {
			return $this->_table_columns[$column_name]['field_options']['source']['data'][$this->$column_name];
		}

		return NULL;
	} // function get_radio_value_string
} // class ORM