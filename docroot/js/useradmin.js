/** user admin **/
$(function() {
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

	$('.permission_form_save').click(function() {
		$(this).closest('form').find('select.xm_include_in_save').each(function(index, select_field) {
			$(select_field).children('option').each(function(index, option_element) {
				$(option_element).attr('selected', 'selected');
			});
		});
	});
});