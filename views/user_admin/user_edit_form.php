<?php

// the form open tag
echo $form_open_tag . EOL;
// the hidden fields
echo implode(EOL, $form_fields_hidden) . EOL;

// If any fields are visible
if ($any_visible) {
	if ($form_options['display_buttons'] && $form_options['display_buttons_at_top']) {
		// the buttons
		echo '<div class="xm_buttons xm_buttons_top">' . implode('', $form_buttons) . '</div>' . EOL;
	}

	// generate the table
	$table = new HTMLTable(array(
		'table_attributes' => array(
			'class' => 'xm_form'
		)
	));

	foreach ($display_order as $column) {
		if (isset($form_field_html[$column])) {
			$table->add_row(array($form_field_html[$column]['label'], $form_field_html[$column]['field']));
		}
	} // foreach

	$table->add_row(array(
		'<label for="send_email">Send Email to User</label>',
		Form::checkbox('send_email', 1, (empty($model->id) ? TRUE : FALSE), array('id' => 'send_email')) . '<div class="xm_field_help xm_field_help_edit" data-xm_field="c_record[user][0][send_email]">' . HTML::icon('circle_question_mark') . 'Checking this will send the user an email containing their login information after the user is saved.</div>',
	));

	// the table html
	echo $table->get_html();

	if ($form_options['display_buttons']) {
		// the buttons
		echo '<div class="xm_buttons">' . implode('', $form_buttons) . '</div>' . EOL;
	}

// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

// the form close tag
echo $form_close_tag;
