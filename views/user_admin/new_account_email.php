<p>An account has been created for you on <?php echo HTML::chars($app_name); ?>. Your login information is as follows:</p>
<p>Username: <?php echo HTML::chars($username); ?><br>
	Password: <span style="font-family: Courier, "Courier New", monospace;"><?php echo HTML::chars($password); ?></span></p>
<p>To login, <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the following link into your browser:</p>
<p style="padding-left: 3em;"><?php echo HTML::chars($url); ?></p>
<p>If you have any problems, please don't hesitate to contact us at <?php echo $support_email; ?></p>
<p>Thank you,<br><?php echo HTML::chars($app_name); ?></p>