<?php

// the form open tag
echo $form_open_tag . EOL;
// the hidden fields
echo implode(EOL, $form_fields_hidden) . EOL;

// If any fields are visible
if ($any_visible) {
	if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
		// the buttons
		echo '<div class="cl4_buttons cl4_buttons_top">' . implode('', $form_buttons) . '</div>' . EOL;
	}

	// generate the table
	$table = new HTMLTable(array(
		'table_attributes' => array(
			'class' => 'cl4_form'
		)
	));

	foreach ($display_order as $column) {
		if (isset($form_field_html[$column])) {
			$table->add_row(array($form_field_html[$column]['label'], $form_field_html[$column]['field']));
		}
	} // foreach

	foreach ($additional_view_data['additional_user_info'] as $_additional) {
		$relationship = $_additional['relationship'];

		$current = $model->$relationship->find_all()->as_array(NULL, 'id');
		$list = ORM::factory($_additional['model'])->find_all()->as_array('id', 'name');
		$table->add_row(array($_additional['name'], Form::checkboxes($_additional['field_name'], $list, $current, array(), array('orientation' => 'vertical'))));
	}

	// the table html
	echo $table->get_html();

	if ($form_options['display_buttons']) {
		// the buttons
		echo '<div class="cl4_buttons">' . implode('', $form_buttons) . '</div>' . EOL;
	}

// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

// the form close tag
echo $form_close_tag;