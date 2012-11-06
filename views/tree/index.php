<div class="tree_actions"><?php echo HTML::anchor(Route::get($route_name)->uri(array('action' => 'add', 'id' => $root_node->id)) . '?c_ajax=1', '&nbsp;Add Item', array('class' => 'cl4_add add_item js_add_item')); ?><a href="" class="expand_all js_expand_all">Expand All</a><a href="" class="collapse_all js_collapse_all">Collapse All</a></div>

<?php echo $tree_html; ?>

<div id="tree_dialog js_tree_dialog"></div>