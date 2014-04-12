<div class="login_box no_access_wrapper">
	<h1>Access Denied</h1>
	<p>You do not have the necessary permissions to access this functionality.</p>
	<?php if (DEBUG_FLAG) { ?>
		<p>Referrer: <?php echo HTML::chars(UTF8::clean($referrer)); ?></p>
	<?php } ?>
</div>