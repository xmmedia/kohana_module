<div class="login_box">
	<h1>Login</h1>
	<p>Login with your email address and password.</p>
	<?php /*<p>If you do not already have an account, <?php echo HTML::anchor('login/register', 'create one') ?> first.</p>*/ ?>

	<?php echo Form::open(Route::get('login')->uri()); ?>
	<?php echo Form::hidden('redirect', $redirect); ?>

	<ul class="cl4_form">
		<li>
			<ul>
				<li class="field_label" style=""><label for="username">Email Address / Username</label></li>
				<li class="field_value"><?php echo Form::input('username', $username, array('size' => 20, 'maxlength' => 100, 'id' => 'username', 'autofocus')); ?></li>
			</ul>
		</li>
		<li>
			<ul>
				<li class="field_label"><label for="password">Password</label></li>
				<li class="field_value"><?php echo Form::password('password', $password, array('size' => 20, 'maxlength' => 255, 'id' => 'password')); ?></li>
			</ul>
		</li>
	</ul>

	<?php if ($add_captcha) { ?>
		<p>Enter the correct username and password above and then type the characters you see in the picture below.</p>
		<?php echo recaptcha_get_html(RECAPTCHA_PUBLIC_KEY); ?>
	<?php } ?>

	<?php
	echo Form::submit(NULL, 'Login', array('class' => 'login_button'));
	echo Form::close();
	?>

	<div class="forgot_link"><?php echo HTML::anchor(Route::get('login')->uri(array('action' => 'forgot')), 'Forgot your password?') ?></div>
	<div class="clear"></div>
</div>