<div class="cl4_list_header">
	<input type="submit" value="Add New Group" class="js_xm_button_link xm_list_button" data-xm_link="<?php echo URL::site(Route::get('user_admin')->uri(array('action' => 'add_group'))); ?>">
	<div class="clear"></div>
</div>

<div class="pagination cl4_nav">
	<div class="cl4_nav_pages"></div>
	<div class="cl4_nav_showing">Showing <?php echo $group_count; ?> of <?php echo $group_count; ?> items</div>
	<div class="clear"></div>
</div>
<?php echo $group_list; ?>
<div class="pagination cl4_nav">
	<div class="cl4_nav_pages"></div>
	<div class="cl4_nav_showing">Showing <?php echo $group_count; ?> of <?php echo $group_count; ?> items</div>
	<div class="clear"></div>
</div>