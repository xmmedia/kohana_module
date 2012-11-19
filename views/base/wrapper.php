
<div id="wrapper">
	<div id="main_content">
	<?php
	echo $pre_message;
	if ( ! empty($message)) {
		echo $message;
	}
	echo $body_html;
	?>
	</div>
	<div class="clear"></div>
</div>
<div class="clear"></div>

<?php echo View::factory('base/footer')
	->set($kohana_view_data); ?>