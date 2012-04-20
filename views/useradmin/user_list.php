<?php echo Form::open(Request::current()); ?>
	<div class="cl4_list_header">
		<?php echo implode('', $list_buttons); ?>
		<div class="clear"></div>
	</div>
<?php echo Form::close(); ?>

<?php echo $nav_html; ?>
<?php echo $user_list; ?>
<?php echo $nav_html; ?>