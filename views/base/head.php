<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<title><?php
if (DEVELOPMENT_FLAG) {
	echo '*** Dev *** ';
}
echo HTML::chars($page_title);
?></title>

<?php if ( ! empty($meta_tags)) {
	foreach ($meta_tags as $name => $content) {
		if ( ! empty($content)) {
			echo TAB, HTML::meta($name, $content), EOL;
		} // if
	} // foreach
} // if

foreach ($styles as $file => $type) {
	echo TAB, HTML::style($file, array('media' => $type)), EOL;
}
?>

	<!--[if lt IE 9]>
	<script><?php echo file_get_contents(DOCROOT . 'js' . DIRECTORY_SEPARATOR . 'html5shiv.min.js'); ?></script>
	<![endif]-->
	<script>
		var in_debug = <?php echo (int) DEBUG_FLAG; ?>;
	</script>
</head>
