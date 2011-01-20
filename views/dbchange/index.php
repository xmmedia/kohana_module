<style>
.db_change_sql { width:100%; height:400px; }
</style>

<?php echo Form::open(); ?>

<p>Select the databases you want to want to run all of the queries on:<br>
[ <a href="" class="select_all">Select All</a> ] [ <a href="" class="select_none">Select None</a> ]<br>
<?php echo $db_checkboxes; ?></p>

<p>The SQL to run:<br>
<?php echo Form::textarea('sql', $db_change_sql, array('class' => 'db_change_sql')); ?></p>

<?php echo Form::submit(NULL, 'Run'); ?>&nbsp;
<?php echo Form::input(NULL, 'Reset', array('type' => 'reset')); ?>&nbsp;
<?php echo Form::input(NULL, 'Clear', array('type' => 'button', 'class' => 'clear')); ?>

<?php echo Form::close(); ?>