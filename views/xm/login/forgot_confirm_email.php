<p>This is a notice to confirm that your password has been reset on the <?php echo HTML::chars($app_name); ?>.</p>
<p>If the you did not change your password, please contact the administrator immediately at: <?php echo HTML::mailto($admin_email); ?></p>

<p>To login, <?php echo HTML::anchor($url, 'click here', array('target' => '_blank'), TRUE); ?> or copy and paste the following link into your browser:</p>
<p style="padding-left: 3em;"><?php echo HTML::chars(URL::site($url, TRUE)); ?></p>

<p>Thank you,<br><?php echo HTML::chars($app_name); ?></p>