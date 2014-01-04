<li<?php echo HTML::attributes(array('data-view_url' => $view_url)); ?>>
	<div class="occurances" title="<?php echo HTML::chars($error_group['occurances']), ' ', Inflector::plural('occurance', $error_group['occurances']); ?>"><?php echo HTML::chars($error_group['occurances']); ?></div>
	<div class="date"><?php echo HTML::chars($error_group['datetime']); ?></div>
	<div class="message"><?php echo HTML::chars($error_group['message']); ?></div>
</li>