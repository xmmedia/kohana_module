<h2><?php
if ($group->id > 0) {
	echo HTML::chars($group->name);
} else {
	echo 'Add New Group';
}
?></h2>

<?php echo $group->get_form(); ?>