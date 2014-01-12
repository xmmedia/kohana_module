<div class="error_admin_container">
	<div class="left_col">
		<div class="error_actions">
			<?php if ($unparsed_count > 0) { ?>
			<div class="action">
				<strong><?php echo (int) $unparsed_count, ' Unparsed ', Inflector::plural('Error', $unparsed_count); ?></strong>
				<?php echo HTML::anchor(Route::get('error_admin')->uri(array('action' => 'parse_errors')), 'Parse ' . Inflector::plural('Error', $unparsed_count)); ?>
			</div>
			<?php } ?>

			<div class="action">
				Filter
				<?php if ( ! $show_resolved) { ?>
					<a href="?show_resolved=1">Show Resolved</a>
				<?php } else { ?>
					<a href="?show_resolved=0">Hide Resolved</a>
				<?php } ?>
			</div>
		</div>
		<ul class="error_groups js_error_groups">
			<?php echo $group_list; ?>
		</ul>
	</div>

	<div class="right_col">
		<?php echo $right_col; ?>
	</div>
</div>