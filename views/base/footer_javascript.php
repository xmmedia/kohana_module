<?php
// if jQuery is in the array of scripts, then include the path to jQuery and also a fallback to a local version
if (isset($scripts['jquery'])) {
	echo HTML::script($scripts['jquery']) . EOL; ?>
<script>window.jQuery || document.write('<script src="/js/jquery.min.js">\x3C/script>')</script>
<?php
	unset($scripts['jquery']);
} // if

// if jQuery UI is in the array of scripts, then include the path to jQuery UI and also a fallback to a local version
if (isset($scripts['jquery_ui'])) {
	echo HTML::script($scripts['jquery_ui']) . EOL; ?>
<script>window.jQuery.ui || document.write('<script src="/js/jquery-ui.min.js">\x3C/script>')</script>
<?php
	unset($scripts['jquery_ui']);
} // if

// Javascript, put all javascript here or in $on_load_js if possible
foreach ($scripts as $file) echo HTML::script($file) . EOL;
?>

<?php // Javascript to run once the page is loaded
if ( ! empty($on_load_js)) { ?>
<script>
$(function() {
<?php echo $on_load_js . EOL; ?>
});
</script>
<?php } // if ?>