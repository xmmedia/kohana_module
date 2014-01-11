<div class="error_admin_container">
	<div class="left_col">
		<ul class="error_groups js_error_groups">
			<li class="resolved_filter js_filter">
				Filter
				<?php if ( ! $show_resolved) { ?>
					<a href="?show_resolved=1">Show Resolved</a>
				<?php } else { ?>
					<a href="?show_resolved=0">Hide Resolved</a>
				<?php } ?>
			</li>
			<?php echo $group_list; ?>
		</ul>
	</div>

	<div class="right_col">
		<?php echo $right_col; ?>
	</div>
</div>