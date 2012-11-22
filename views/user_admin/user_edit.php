<h2><?php
if ($user->id > 0) {
	echo HTML::chars($user->first_name . ' ' . $user->last_name);
} else {
	echo 'Add New User';
}
?></h2>

<?php echo $user->get_form(); ?>