<h1 class="account_page_title">Change Password</h1>

<div class="account_form">
	<?php echo $form_open; ?>

	<div class="field">
		<?php echo Form::label('current_password', 'Current Password'),
			Form::password('current_password', '', array('size' => 30, 'maxlength' => 255, 'autofocus')); ?>
	</div>
	<div class="field">
		<?php echo Form::label('new_password', 'New Password'),
			Form::password('new_password', '', array('size' => 30, 'maxlength' => 255)); ?>
	</div>
	<div class="field">
		<?php echo Form::label('new_password_confirm', 'Confirm New Password'),
			Form::password('new_password_confirm', '', array('size' => 30, 'maxlength' => 255)); ?>
	</div>

	<div class="buttons"><?php echo Form::button(NULL, 'Change Password'), HTML::anchor($default_uri, 'Cancel'); ?></div>

	</form>
</div>