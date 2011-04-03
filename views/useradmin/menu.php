
<nav class="basic_nav">
	<ul>
		<?php if (Auth::instance()->allowed('useradmin/index')) { ?><li><?php echo HTML::anchor(Route::get('useradmin')->uri(), 'Users'); ?></li><?php } // if ?>
		<?php if (Auth::instance()->allowed('useradmin/group/index')) { ?><li><?php echo HTML::anchor(Route::get('useradmin')->uri(array('action' => 'groups')), 'Groups'); ?></li><?php } // if ?>
	</ul>
</nav>
<div class="clear"></div>
<?php if ( ! empty($page_title)) { ?><h1><?php echo HTML::chars($page_title); ?></h1><?php } ?>
