<?php echo Message::display(),
	Form::open(Route::get($route_name)->uri(array('action' => 'edit', 'id' => $node->id)), array('method' => 'POST')),
		$node->get_field_layout('name'),
		// '<div>', Form::label('sibling_id', 'Change Order/Move After'), $sibling_select, '</div>',
	Form::close();