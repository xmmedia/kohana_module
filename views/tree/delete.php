<?php echo Message::display(),
	Form::open(Route::get('tree')->uri(array('action' => 'delete', 'id' => $node->id)), array('method' => 'POST')),
		'<p>Are you sure you want to delete <em>', HTML::chars($node->name), '</em> of <em>', HTML::chars($parent['name']), '</em>?</p>',
		Form::submit('delete_confirm', 'Yes'), Form::input_button('delete_confirm', 'No'),
		'<p>', Form::checkbox('keep_children', 1, FALSE, array('id' => 'keep_children')), Form::label('keep_children', 'Move children to parent node'), '</p>',
	Form::close();