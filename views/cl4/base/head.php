<!DOCTYPE html>
<html <?php if (isset($language)) { ?>lang="<?php echo HTML::chars($language); ?>" <?php } ?>class="no-js">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
	<title><?php
if (DEVELOPMENT_FLAG) {
	echo '*** ' . HTML::chars(SHORT_NAME . ' v' . APP_VERSION) . ' Development Site *** ';
}
if ( ! empty($page_title) && trim($page_title) != '') {
	echo HTML::chars($page_title) . ' - ';
}
echo HTML::chars(SHORT_NAME . ' v' . APP_VERSION); ?></title>
<?php if ( ! empty($meta_tags)) {
	foreach ($meta_tags as $name => $content) {
		if ( ! empty($content)) {
			echo TAB . HTML::meta($name, $content) . EOL;
		} // if
	} // foreach
} // if
?>
	<!--[if lte IE 9]><link rel="stylesheet" href="/css/1140ie.css" type="text/css" media="screen" /><![endif]-->
<?php
foreach ($styles as $file => $type) echo TAB . HTML::style($file, array('media' => $type)) . EOL;
// http://www.modernizr.com fixes missing html5 elements in IE and detects for new HTML5 features; this needs to be loaded here so the HTML5 tags will show in IE
echo "\t" . HTML::script($modernizr_path) . EOL; ?>
	<script>
		var cl4_in_debug = <?php echo (int) DEBUG_FLAG; ?>;
	</script>
</head>
