<p>A password reset request has been initiated for your account on the <?php echo HTML::chars($app_name); ?>.</p>

<p style="padding-left: 3em;"><?php echo HTML::anchor($url, 'Reset your ' . HTML::chars($app_name) . ' password', array('target' => '_blank'), TRUE); ?></p>

<p>Upon clicking the link, you will be able to enter a new password.</p>
<p>If this request was not made by you, you can choose to ignore this email or contact the administrator at: <?php echo HTML::mailto($admin_email); ?></p>
<p>Thank you,<br>
	<?php echo HTML::chars($app_name); ?></p>