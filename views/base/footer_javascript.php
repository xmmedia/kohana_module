<?php
// Javascript, put all javascript here or in $on_load_js if possible
foreach ($scripts as $file) :
	echo HTML::script($file), EOL;
endforeach;
?>

<?php
// Javascript to run once the page is loaded
if ( ! empty($on_load_js)) : ?>
<script>
$(function() {
<?php echo $on_load_js, EOL; ?>
});
</script>
<?php endif; ?>