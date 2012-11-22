<?php

// If any fields are visible
if ($any_visible) {
	// generate the table
	$table = new HTMLTable($form_options['table_options']);

	foreach ($display_order as $column) {
		if (isset($form_field_html[$column])) {
			$table->add_row(array($form_field_html[$column]['label'], $form_field_html[$column]['field']));
		}
	} // foreach

	// the table html
	echo $table->get_html();

// If no fields are visible
} else {
	echo '<p>No fields are visible.</p>';
}

$submit_button_options = array(
	'class' => 'js_cl4_button_link',
	'data-cl4_link' => URL::site(Route::get('user_admin')->uri(array('action' => 'groups'))),
);

echo '<div class="cl4_buttons">' . Form::submit(NULL, __('Return to List'), $submit_button_options) . '</div>';