<?php echo Message::display(),
	Form::open(Route::get($route_name)->uri(array('action' => 'add')), array('method' => 'POST')),
		Form::hidden('parent_id', $parent_node->id),
		'<p>Add a child node to <em>', HTML::chars($parent_node->name()), '</em>:</p>',
		$tree_node->get_field_layout('name'),
		'<div>', Form::label('sibling_id', 'Add after'), $sibling_select, '</div>',
	Form::close();