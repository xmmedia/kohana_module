<div class="cl4_delete_confirm_message">
	<?php echo Form::open(Request::current()); ?>
	Are you sure you want to delete the following user?
	<?php
	echo Form::submit('cl4_delete_confirm', __('Yes'));
	echo Form::submit('cl4_delete_confirm', __('No'));
	echo Form::close();
	?>
</div>