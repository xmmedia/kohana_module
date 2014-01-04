<div class="error_status">
	<?php echo $resolve_link; ?>
</div>

<div class="error_header">
	<h1><?php echo HTML::chars($error_log->message); ?></h1>

	<h2 class="occurances"><div><?php echo $occurance_count; ?></div> <?php echo Inflector::plural('Occurance', $occurance_count); ?></h2>
	<h2 class="occurred">Occurred: <?php echo HTML::Chars($error_log->datetime); ?></h2>
</div>

<div class="error_details">
	<ul class="tabs js_tabs">
		<li><a href="" rel="summary">Summary</a></li>
		<li><a href="" rel="backtrace">Backtrace</a></li>
		<li><a href="" rel="server">Server</a></li>
		<li><a href="" rel="post">Post</a></li>
		<li><a href="" rel="get">Get</a></li>
		<li><a href="" rel="files">Files</a></li>
		<li><a href="" rel="cookie">Cookies</a></li>
		<li><a href="" rel="session">Session</a></li>
		<li><a href="" rel="similar">Similar Errors</a></li>
	</ul>

	<div class="details js_details" rel="summary" style="display: block;">
		<div>
			<div class="label">File:Line</div>
			<div class="value"><?php echo HTML::chars($error_log->file . ':' . $error_log->line); ?></div>
		</div>
		<div>
			<div class="label">Remote Address</div>
			<div class="value"><?php echo HTML::chars($error_log->remote_address); ?></div>
		</div>
		<div>
			<div class="label">URL</div>
			<div class="value"><?php echo HTML::chars($error_log->url); ?></div>
		</div>
		<?php if ( ! empty($html_file_link)) { ?>
		<div>
			<div class="label">HTML File</div>
			<div class="value"><?php echo $html_file_link; ?></div>
		</div>
		<?php } ?>
	</div>

	<div class="details js_details" rel="server">
		<?php echo $server_items; ?>
	</div>

	<div class="details js_details" rel="post">
		<?php echo $post_items; ?>
	</div>

	<div class="details js_details" rel="get">
		<?php echo $get_items; ?>
	</div>

	<div class="details js_details" rel="cookie">
		<?php echo $cookie_items; ?>
	</div>

	<div class="details js_details" rel="session">
		<?php echo $session_items; ?>
	</div>
</div>