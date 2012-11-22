
<nav class="basic_nav">
	<ul>
		<?php if (Auth::instance()->allowed('user_admin/index')) { ?><li><?php echo HTML::anchor(Route::get('user_admin')->uri(), 'Users'); ?></li><?php } // if ?>
		<?php if (Auth::instance()->allowed('user_admin/group/index')) { ?><li><?php echo HTML::anchor(Route::get('user_admin')->uri(array('action' => 'groups')), 'Groups'); ?></li><?php } // if ?>
	</ul>
</nav>
<div class="clear"></div>
<?php if ( ! empty($page_title)) { ?><h1><?php echo HTML::chars($page_title); ?></h1><?php } ?>
