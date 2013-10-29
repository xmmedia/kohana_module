<?php echo Form::open($form_action, array(
	'class' => 'js_xm_model_select_form',
	'method' => 'get',
)); ?>
	<div class="xm_model_select_container">
		<?php echo HTML::chars(__('Manage')); ?>: <?php echo $model_select; ?>
		<input type="button" value="Go" class="js_xm_model_select_go">
	</div>
</form>