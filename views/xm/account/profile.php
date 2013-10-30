<h1>Edit Profile</h1>

<p>To change your name or email address/username, use the form below:</p>

<?php echo $edit_fields; ?>

<br><br>
<h1>Change Password</h1>
<p>To change your password, use the form below:</p>

<?php echo Form::open(URL::site(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'password'))));
	echo Form::hidden('form', 'password');

$table = new HTMLTable(array(
	'table_attributes' => array(
		'class' => 'xm_form',
	),
));

$table->add_row(array(
	'<label>Your Current Password</label>',
	Form::password('current_password', '', array('class' => 'text', 'size' => 30, 'maxlength' => 255)),
));
$table->add_row(array(
	'<label>New Password</label>',
	Form::password('new_password', '', array('class' => 'text', 'size' => 30, 'maxlength' => 255)),
));
$table->add_row(array(
	'<label>Confirm New Password</label>',
	Form::password('new_password_confirm', '', array('class' => 'text', 'size' => 30, 'maxlength' => 255)),
));

echo $table->get_html();

?>

<div class="xm_buttons">
<?php
echo Form::submit('xm_submit', 'Save');
echo Form::input('xm_cancel', __('Cancel'), array(
	'type' => 'button',
	'class' => 'js_xm_button_link',
	'data-xm_link' => URL::site(Route::get(Route::name(Request::current()->route()))->uri(array('action' => 'cancel'))),
));
?>
</div>

<?php echo Form::close();