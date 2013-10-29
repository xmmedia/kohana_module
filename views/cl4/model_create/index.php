<h2 style="margin-top: 0;">Model Code Generation</h2>
<p>You have to create at least one model for each database table before you can use it within ORM.
The following tool can help generate a starting point from your database table.
Just select a table and click "create" to see some sample model code in the textarea below.</p>
<p>If you want to use this code, create a new file in your application/Classes/Model directory and name it /Table/Name.php
and copy and paste the code in to this file. You will then want to make sure the meta data is all correct,
espcially with respect to displaying sensitive data.</p>
<p>Select a table to generate the cl4/orm model code:
<?php
echo Form::select('db_group', $db_list, $db_group, array('id' => 'db_group')), '&nbsp;',
	Form::select('table_name', $table_list, $table_name, array('id' => 'table_name')), '&nbsp;',
	Form::input('create', 'Create', array('type' => 'button', 'id' => 'create_model')),
	Form::textarea('', '', array(
		'id' => 'model_code_container',
		'class' => 'cl4_model_code_container',
		'style' => 'font-family: "Courier New", Courier, monospace;',
	));
?>
</p>