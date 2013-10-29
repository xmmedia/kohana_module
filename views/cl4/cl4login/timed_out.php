<?php
$logout_uri = Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'logout'));
?>
<div class="login_box timed_out_wrapper">
	<h1>Timed Out</h1>
	<p>Your login has timed out. To continue using your current login, enter your password.</p>
	<p><?php echo HTML::anchor($logout_uri, 'Click here to logout or login as different user.'); ?></p>

	<?php echo Form::open(Route::get('login')->uri()); ?>
	<?php echo Form::hidden('redirect', $redirect); ?>
	<?php echo Form::hidden('timed_out', 1); ?>

	<ul class="cl4_form">
		<li>
			<ul>
				<li class="field_label"><label>Username</label></li>
				<li class="field_value"><?php echo HTML::chars($username) . Form::hidden('username', $username); ?></li>
			</ul>
		</li>
		<li>
			<ul>
				<li class="field_label"><label>Password</label></li>
				<li class="field_value"><?php echo Form::password('password', '', array('size' => 20, 'maxlength' => 255, 'autofocus')) ?></li>
			</ul>
		</li>
	</ul>
	<div class="clear"></div>

<?php
echo Form::submit(NULL, 'Login');
echo Form::close();
?>
</div>