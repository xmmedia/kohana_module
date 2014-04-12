<h1 class="account_page_title">Your Profile</h1>

<div class="account_form">
	<?php echo $form_open; ?>

	<?php foreach ($columns as $column_name) : ?>
		<div class="field"><?php echo $user->get_field_layout($column_name); ?></div>
		<?php if ($column_name == 'username') : ?>
			<div class="field"><label>Password</label><?php echo HTML::anchor($password_uri, 'Change password'); ?></div>
		<?php endif ?>
	<?php endforeach ?>

	<div class="buttons"><?php echo Form::button(NULL, 'Save'), HTML::anchor($default_uri, 'Reset'); ?></div>

	</form>
</div>