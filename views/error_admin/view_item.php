<div>
	<div class="label"><?php echo HTML::chars($key); ?></div>
	<div class="value">
	<?php if ($pre) { ?>
		<pre><?php echo $value; ?></pre>
	<?php } else { ?>
		<?php echo $value; ?>
	<?php } ?>
	</div>
</div>