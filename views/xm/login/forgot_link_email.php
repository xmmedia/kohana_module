<p>A password reset request has been initiated for your account on the <?php echo HTML::chars($app_name); ?>.</p>

<p>Please <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the link below into your browser to reset your password:</p>
<p style="padding-left: 3em;"><?php echo HTML::chars(URL::site($url, TRUE)); ?></p>

<p>Upon clicking the link, a new password will be sent to you.</p>
<p>If this request was not made by you, you can choose to ignore this email or contact the administrator at: <?php echo HTML::mailto($admin_email); ?></p>
<p>Thank you,<br><?php echo HTML::chars($app_name); ?></p>