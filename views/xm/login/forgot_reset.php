<div class="login_box">
	<h1>Enter a New Password</h1>

	<?php echo Form::open(Request::current()),
		Form::hidden('new_password_submitted', 1),
		Form::hidden('token', $token); ?>

	<div class="field">
		<label for="password" class="block">New Password</label>
		<?php echo Form::password('password', NULL, array('size' => 20, 'maxlength' => 255, 'id' => 'password', 'autofocus')); ?>
	</div>
	<div class="field">
		<label for="password_confirm" class="block">Retype Password</label>
		<?php echo Form::password('password_confirm', NULL, array('size' => 20, 'maxlength' => 255, 'id' => 'password_confirm')); ?>
	</div>

	<?php echo Form::submit(NULL, 'Submit'),
		Form::close(); ?>

	<div class="go_link"><?php echo HTML::anchor(Route::get('login')->uri(), 'Login') ?></div>
</div>