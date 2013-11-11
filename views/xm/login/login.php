<div class="login_box">
	<h1>Login</h1>

	<?php echo Form::open(Route::get('login')->uri()),
		Form::hidden('redirect', $redirect); ?>

	<div class="field">
		<label for="username">Email Address</label>
		<?php echo Form::input('username', $username, array('size' => 20, 'maxlength' => 100, 'id' => 'username', 'autofocus')); ?>
	</div>
	<div class="field">
		<label for="password">Password</label>
		<?php echo Form::password('password', $password, array('size' => 20, 'maxlength' => 255, 'id' => 'password')); ?>
	</div>

	<?php if ($add_captcha) { ?>
		<p>Enter the correct username and password above and then type the characters you see in the picture below.</p>
		<?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?>
	<?php } ?>

	<?php echo Form::submit(NULL, 'Login'),
		Form::close(); ?>

	<div class="go_link"><?php echo HTML::anchor(Route::get('login')->uri(array('action' => 'forgot')), 'Forgot your password?') ?></div>
</div>