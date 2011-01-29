
<nav class="basic_nav">
	<ul>
		<li><?php echo HTML::anchor(Route::get('useradmin')->uri(), 'Users'); ?></li>
		<li><?php echo HTML::anchor(Route::get('useradmin')->uri(array('action' => 'groups')), 'Groups'); ?></li>
	</ul>
</nav>
<div class="clear"></div>
<?php if ( ! empty($page_title)) { ?><h1><?php echo HTML::chars($page_title); ?></h1><?php } ?>
