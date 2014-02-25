<?php  if ( ! empty($messages)) { ?>
<ul class="xm_message js_xm_message">
<?php
	foreach ($messages as $message) {
		echo '<li class="' . $level_to_class[$message['level']] . ' js_xm_message_item"><a href="" class="hide js_hide">' . HTML::icon('circle_remove') . '</a>' . $message['message'] . '</li>' . EOL;
	}
?>
</ul>
<?php } ?>
