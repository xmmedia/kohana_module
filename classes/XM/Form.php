<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Form extends cl4_Form {
	public static function month($name, $selected, $attributes = NULL, $options = array()) {
		$options += array(
			'include_month_number' => FALSE,
		);

		$months = array();
		for ($i = 1; $i <= 12; $i ++) {
			$months[$i] = ($options['include_month_number'] ? $i . ' - ' : '') . date('F', Date::MONTH * $i - (Date::DAY * 2));
		}

		return Form::select($name, $months, $selected, $attributes, $options);
	} // function month

	public static function weekday($name, $selected, $attributes = NULL, $options = array()) {
		$options += array(
			'begins_on_sunday' => TRUE,
		);

		$days = array(
			2 => __('Monday'),
			3 => __('Tuesday'),
			4 => __('Wednesday'),
			5 => __('Thursday'),
			6 => __('Friday'),
			7 => __('Saturday'),
		);
		if ($options['begins_on_sunday']) {
			$days = Arr::unshift($days, 1, __('Sunday'));
		} else {
			$days[1] = __('Sunday');
		}

		return Form::select($name, $days, $selected, $attributes, $options);
	} // function weekday

	public static function user_select($name, $selected, $attributes = NULL, $options = array()) {
		$users = DB::select('id', array(DB::expr("CONCAT_WS(' ', first_name, last_name)"), 'user_name'))
			->from('user')
			->where_expiry()
			->execute()
			->as_array('id', 'user_name');

		return Form::select($name, $users, $selected, $attributes, $options);
	} // function user_select

	/**
	 * Creates radio buttons for a form, but returns as an array.
	 *
	 * @param string $name       The name of these radio buttons.
	 * @param array  $source     The source to build the inputs from.
	 * @param mixed  $selected   The selected input.
	 * @param array  $attributes Attributes to apply to the radio inputs.
	 * @param array  $options    Options to modify the creation of our inputs.
	 *        orientation => the way that radio buttons and checkboxes are laid out, allowed: horizontal, vertical, table, table_vertical (puts text above the <input> separated by a <br />) (default: horizontal)
	 *        radio_attributes => an array where the keys are the radio values and the values are arrays of attributes to be added to the radios
	 *
	 * @return string
	 */
	public static function radio_array($name, $source, $selected = NULL, array $attributes = NULL, array $options = array()) {
		$html = '';

		$default_options = array(
			'view' => NULL,
			'replace_spaces' => TRUE,
			'table_tag' => true,
			'escape_label' => TRUE,
			'source_value' => Form::$default_source_value,
			'source_label' => Form::$default_source_label,
			'radio_attributes' => array(),
			'label_attributes' => array(),
		);
		$options += $default_options;

		if (empty($attributes['id'])) {
			// since we have no ID, but we need one for the labels, so just use a unique id
			$attributes['id'] = uniqid();
		}

		$radios = array();
		foreach ($source as $radio_key => $radio_value) {
			$checked = ($selected == $radio_key);

			// make an attribute for this radio based on the current id plus the value of the radio
			$this_attributes = Arr::overwrite($attributes, array('id' => $attributes['id'] . '-' . $radio_key));

			if (isset($options['radio_attributes'][$radio_key])) {
				$this_attributes = HTML::merge_attributes($this_attributes, $options['radio_attributes'][$radio_key]);
			}

			$label_attributes = array(
				'for' => $this_attributes['id'],
			);
			if (isset($options['label_attributes'][$radio_key])) {
				$label_attributes = HTML::merge_attributes($label_attributes, $options['label_attributes'][$radio_key]);
			}

			$radios[] = array(
				'radio' => Form::radio($name, $radio_key, $checked, $this_attributes),
				'label' => $radio_value,
				'label_tag' => '<label' . HTML::attributes($label_attributes) . '>',
			);
		} // foreach

		return $radios;
	} // function radio_array

	/**
	 * Returns a string of hidden fields.
	 * If `$name` is an array, the keys will be used as the field names and the values will be the field values.
	 * If `$name` is a string, the name will be used as the name for all the fields and the values will the values for all the fields (they keys will be ignored).
	 * In the later case, the name should probably end with "[]".
	 *
	 * @param  array   $name    The array of names and values or the name of the fields.
	 * @param  array   $values  If applicable, the values of the fields.
	 * @return  string
	 */
	public static function hidden_array($name, $values = NULL) {
		$html = '';

		if (is_array($name)) {
			foreach ($name as $_name => $_value) {
				$html .= Form::hidden($_name, $_value);
			}
		} else {
			foreach ($values as $value) {
				$html .= Form::hidden($name, $value);
			}
		}

		return $html;
	} // function hidden_array
} // class XM_Form