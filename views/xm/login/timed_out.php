<div class="login_box timed_out_wrapper">
	<h1>Timed Out</h1>
	<p>Your login has timed out. To continue using your current login, enter your password.</p>

	<?php echo Form::open(Route::get('login')->uri()),
		Form::hidden('redirect', $redirect),
		Form::hidden('timed_out', 1); ?>

	<div class="field">
		<label>Username</label>
		<?php echo HTML::chars($username), Form::hidden('username', $username); ?>
	</div>
	<div class="field">
		<label for="password">Password</label>
		<?php echo Form::password('password', '', array('size' => 20, 'maxlength' => 255, 'id' => 'password', 'autofocus')) ?>
	</div>

	<?php echo Form::submit(NULL, 'Login'),
		Form::close(); ?>

	<div class="go_link"><?php echo HTML::anchor($logout_uri, 'Logout or login as different user'); ?></div>
</div>