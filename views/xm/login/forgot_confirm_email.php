<p>This is a notice to confirm that your password has been changed on the <?php echo HTML::chars($app_name); ?>.</p>
<p>If the you did not change your password, please contact the administrator immediately at: <?php echo HTML::mailto($admin_email); ?></p>

<p><?php echo HTML::anchor($url, 'Login to ' . HTML::chars($app_name), array('target' => '_blank'), TRUE); ?></p>

<p>Thank you,<br>
	<?php echo HTML::chars($app_name); ?></p>