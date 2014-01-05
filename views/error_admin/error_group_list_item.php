<li<?php echo HTML::attributes(array('data-view_url' => $view_url)); ?>>
	<div class="occurances" title="<?php echo HTML::chars($occurances), ' ', Inflector::plural('occurance', $occurances); ?>"><?php echo HTML::chars($occurances); ?></div>
	<div class="date"><?php echo HTML::chars($error_log->datetime); ?></div>
	<div class="message"><?php echo HTML::anchor($view_url, HTML::chars(Text::limit_chars($error_log->message, 230)), array('title' => $error_log->message)); ?></div>
</li>