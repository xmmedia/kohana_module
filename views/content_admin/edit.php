<div class="container">
	<div class="row">
		<div class="twelvecol">
			<h1>Edit: <?php echo HTML::chars($content_item->name); ?></h1>
			<?php if ( ! empty($content_item->description)) { ?>
			<p><strong>Description:</strong> <?php echo nl2br(HTML::chars($content_item->description)); ?></p>
			<?php } // if ?>
			<?php if ( ! empty($content_item->instructions)) { ?>
			<p><strong>Instructions:</strong> <?php echo nl2br(HTML::chars($content_item->instructions)); ?></p>
			<?php } // if ?>
			<?php if (Kohana::find_file('views', 'content_admin/style_guide')) { ?>
			<p><a href="" class="js_show_style_guide">Show Style Guide</a></p>
			<div class="content_style_guide js_content_style_guide"><?php echo View::factory('content_admin/style_guide'); ?></div>
			<?php } // if ?>

			<?php echo $form_open, Form::hidden('popup', (int) $popup); ?>
			<p><?php echo Form::checkbox('immediately_live', 1, TRUE, array('id' => 'immediately_live')), Form::label('immediately_live', 'Make the changes live immediately'); ?></p>
			<?php echo $content_item->get_field('content'); ?>
			<p><?php echo $content_history->get_field_layout('comments', 'block'); ?></p>
			<div class="xm_buttons"><?php echo Form::submit(NULL, 'Save'),
				Form::input_button(NULL, 'Reset', array('class' => 'js_xm_button_link', 'data-xm_link' => URL::site(Route::get('content_admin')->uri(array('action' => 'edit', 'id' => $content_item->id)) . ($popup ? '?popup=1' : '')))),
				Form::input_button(NULL, 'Cancel', array('class' => 'js_xm_button_link content_admin_cancel', 'data-xm_link' => URL::site(Route::get('content_admin')->uri(array('action' => 'cancel'))))); ?></div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>