<?php echo Form::open(Request::current()); ?>
	<div class="cl4_list_header">
		<input type="submit" value="Add New User" class="cl4_button_link_form cl4_list_button" data-cl4_form_action="<?php echo URL::site(Route::get('useradmin')->uri(array('action' => 'add'))); ?>">
		<div class="clear"></div>
	</div>
<?php echo Form::close(); ?>

<?php echo $nav_html; ?>
<?php echo $user_list; ?>
<?php echo $nav_html; ?>