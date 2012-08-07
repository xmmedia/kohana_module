<div class="tree_actions"><?php echo HTML::anchor(Route::get('tree')->uri(array('action' => 'add', 'id' => $root_node->id)) . '?c_ajax=1', '&nbsp;Add Item', array('class' => 'cl4_add add_item')); ?><a href="" class="expand_all">Expand All</a><a href="" class="collapse_all">Collapse All</a></div>

<?php echo $tree_html; ?>

<div id="tree_dialog"></div>