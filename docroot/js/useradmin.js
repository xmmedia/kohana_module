/** user admin **/
$(function() {
	/**
	* Moves select options from one select to another when a button (or link) is clicked
	* The from and to selects are based on data parameters on the button
	*/
	$('.move_select_options').click(function() {
		$from_select = $('[name="' + $(this).data('xm_from_select') + '"]');
		$to_select = $('[name="' + $(this).data('xm_to_select') + '"]');

		$from_select.children(':selected').each(function(index, option_element) {
			$to_select.append(option_element);
		});

		$from_select.val('');
		$to_select.val('');

		return false;
	});

	/**
	* When the button is clicked (likely a submit button) all of the options inside
	* selects with the class xm_include_in_save are selected
	*/
	$('.permission_form_save').click(function() {
		$(this).closest('form').find('select.xm_include_in_save').each(function(index, select_field) {
			$(select_field).children('option').each(function(index, option_element) {
				$(option_element).attr('selected', 'selected');
			});
		});
	});

	/**
	* Moves a group of select options from one select to another when a button is clicked
	* The value of the permission group select in it is used as the permission ids, split on commas.
	* The from, to and permission group select are based on data parameters on the button
	*/
	$('.move_multiple_select_options').click(function() {
		$perm_group_select = $('[name="' + $(this).data('xm_perm_group_select') + '"]');
		move_items = $perm_group_select.val().split(',');

		$from_select = $('[name="' + $(this).data('xm_from_select') + '"]');
		$to_select = $('[name="' + $(this).data('xm_to_select') + '"]');

		$.each(move_items, function(index, permission_id) {
			$to_select.append($from_select.children('option[value="' + permission_id + '"]').detach());
		});

		$perm_group_select.val('');

		return false;
	});
});