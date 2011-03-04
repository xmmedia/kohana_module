<p>Your account <?php echo HTML::chars($app_name); ?> as been updated. Your new login information is as follows:</p>
<p>Username: <?php echo $username; ?><br>
Password: <?php echo ($password === FALSE ? '<em>Your current password</em>' : $password); ?></p>
<p>To login, <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the following link into your browser:</p>
<p><?php echo HTML::chars($url); ?></p>
<p>If you have problems, please contact the administrator at <?php echo $support_email; ?>.</p>
<p>Thank you,<br><?php echo HTML::chars($app_name); ?></p>