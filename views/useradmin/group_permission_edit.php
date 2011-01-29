<div class="group_perm_edit">

<h1><?php echo HTML::chars($group->name); ?></h1>
<?php if ( ! empty($group->description)) { ?><p>Description: <?php echo HTML::chars($group->description); ?></p><?php } ?>

<?php echo Form::open(); ?>

<div class="perm_available">
	<h4>The available permissions</h4>
	<?php echo $available_perms_select; ?>
</div>
<div class="perm_buttons">
	<?php echo Form::input_button(NULL, 'Add »', array(
		'class' => 'move_select_options',
		'data-xm_from_select' => 'available_permissions[]',
		'data-xm_to_select' => 'current_permissions[]',
	)); ?>
	<?php echo Form::input_button(NULL, '« Remove', array(
		'class' => 'move_select_options',
		'data-xm_from_select' => 'current_permissions[]',
		'data-xm_to_select' => 'available_permissions[]',
	)); ?>
</div>
<div class="perm_current">
	<h4>The current permissions</h4>
	<?php echo $current_perms_select; ?>
</div>
<div class="clear"></div>

<div class="cl4_buttons">
	<?php echo Form::submit(NULL, 'Save', array('class' => 'permission_form_save')); ?>
	<?php echo Form::input_button(NULL, 'Reset', array(
		'class' => 'cl4_button_link',
		'data-cl4_link' => URL::site(Request::instance()->uri()),
	)); ?>
	<?php echo Form::input_button(NULL, 'Cancel', array(
		'class' => 'cl4_button_link',
		'data-cl4_link' => URL::site(Route::get('useradmin')->uri(array('action' => 'cancel_group'))),
	)); ?>
</div>

<?php echo Form::close(); ?>
</div>