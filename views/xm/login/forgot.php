<div class="login_box">
	<h1>Reset Your Password</h1>

	<?php echo Form::open(Request::current()); ?>
	<p><label for="reset_username">To start, enter your email address:</label> <?php echo Form::input('reset_username', NULL, array('autofocus', 'size' => 30, 'maxlength' => 100, 'id' => 'reset_username')); ?></p>
	<?php echo Form::submit(NULL, 'Reset Password'), Form::close(); ?>
</div>