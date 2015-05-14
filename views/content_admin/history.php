<h1>History: <?php echo HTML::chars($content_item->name); ?></h1>
<p><?php echo HTML::anchor(Route::get('content_admin')->uri(), '< Back to Content Admin'); ?> | <?php echo HTML::anchor(Route::get('content_admin')->uri(array('action' => 'edit', 'id' => $content_item->id)), 'Edit Content'); ?></p>
<?php echo $content_history_html; ?>

<div id="js_content_admin_dialog"></div>