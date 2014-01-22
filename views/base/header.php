<header>
	<div class="top_nav_wrapper">
		<nav>
			<?php echo Nav::get('private'); ?>
 			<?php echo Nav::get('private_right'); ?>
		</nav>
	</div>

	<div class="page_top">
		<div class="page_top_logo"><a href="<?php echo URL::base(); ?>"><?php echo (isset($page_top_logo) ? $page_top_logo : HTML::chars(LONG_NAME)); ?></a></div>
	</div>
</header>