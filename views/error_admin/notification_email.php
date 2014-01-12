<p>The following error occured on <?php echo HTML::chars(LONG_NAME); ?>:</p>

<div style="padding-top: 10px; padding-bottom: 10px; padding-left: 30px;">
	<div>
		<div style="color: #9f9f9f;">Error Message</div>
		<div><?php echo HTML::chars($error_log_model->message), ' in ', HTML::chars($error_log_model->clean_file()), ' on line ', HTML::chars($error_log_model->line); ?></div>
	</div>

	<div style="padding-top: 20px;">
		<div style="color: #9f9f9f;">Date &amp; Time</div>
		<div><?php echo HTML::chars($error_log_model->datetime); ?></div>
	</div>

	<?php if ( ! empty($error_log_model->url)) { ?>
	<div style="padding-top: 20px;">
		<div style="color: #9f9f9f;">URL</div>
		<div><?php echo HTML::chars($error_log_model->url); ?></div>
	</div>
	<?php } ?>

	<?php if ( ! empty($trace_file_list)) { ?>
	<div style="padding-top: 20px;">
		<div style="color: #9f9f9f;">Backtrace</div>
		<div style="font-family: 'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace;">
			<?php
			foreach ($trace_file_list as $item) {
				echo HTML::chars($item) . '<br>';
			}
			?>
		</div>
	</div>
	<?php } ?>
</div>

<p>There are a total of <strong>unresolved <?php echo $occurances, ' ', Inflector::plural('occurance', $occurances); ?></strong> of this error.</p>
<p>View the error in the <?php echo HTML::anchor($view_error_url, HTML::chars(LONG_NAME) . ' Error Admin', array('target' => '_blank', 'style' => 'color: #489dcf;')); ?>.</p>