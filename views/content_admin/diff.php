<h1>Diffences: <?php echo HTML::chars($content_item->name); ?></h1>
<p><?php echo HTML::anchor(Route::get('content_admin')->uri(array('action' => 'history', 'id' => $content_item->id)), '< Back to Content History'); ?> | <?php echo HTML::anchor(Route::get('content_admin')->uri(array('action' => 'edit', 'id' => $content_item->id)), 'Edit Content'); ?></p>

<div class="content_admin_diff_header">
	<div>
		<strong>Original</strong><br>
		<?php echo HTML::chars($prev_content_history->creation_date . ' by ' . $prev_content_history->creation_user->name()); ?>
	</div>
	<div>
		<strong>New</strong><br>
		<?php echo HTML::chars($content_history->creation_date . ' by ' . $content_history->creation_user->name()); ?><br>
		<?php echo Form::open(NULL, array('method' => 'GET')); ?>
		<label for="compare_to">Compare To</label> <?php echo $history_select; ?>
		<?php echo Form::close(); ?>
	</div>
</div>

<div class="content_diff"><?php echo $diff; ?></div>

	<p><a href="" class="js_content_admin_hide_changes">Hide Changes</a></p>

	<p><a href="" class="js_content_admin_show_content" rel="new" data-name="New">Show New</a></p>
	<div class="content_diff_all js_content_diff_all" rel="new"><?php echo $content_history->content; ?></div>

	<p><a href="" class="js_content_admin_show_content" rel="original" data-name="Original">Show Original</a></p>
	<div class="content_diff_all js_content_diff_all" rel="original"><?php echo $prev_content_history->content; ?></div>