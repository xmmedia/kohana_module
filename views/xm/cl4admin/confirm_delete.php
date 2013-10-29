<div class="xm_delete_confirm_message">
	<?php echo Form::open(Request::current()); ?>
	Are you sure you want to delete the following item from <?php echo HTML::chars($object_name); ?>?
	<?php
	echo Form::submit('xm_delete_confirm', __('Yes'));
	echo Form::submit('xm_delete_confirm', __('No'));
	echo Form::close();
	?>
</div>