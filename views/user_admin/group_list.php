<div class="xm_list_header">
	<input type="submit" value="Add New Group" class="js_xm_button_link xm_list_button" data-xm_link="<?php echo URL::site(Route::get('user_admin')->uri(array('action' => 'add_group'))); ?>">
	<div class="clear"></div>
</div>

<div class="pagination xm_nav">
	<div class="xm_nav_pages"></div>
	<div class="xm_nav_showing">Showing <?php echo $group_count; ?> of <?php echo $group_count; ?> items</div>
	<div class="clear"></div>
</div>
<?php echo $group_list; ?>
<div class="pagination xm_nav">
	<div class="xm_nav_pages"></div>
	<div class="xm_nav_showing">Showing <?php echo $group_count; ?> of <?php echo $group_count; ?> items</div>
	<div class="clear"></div>
</div>