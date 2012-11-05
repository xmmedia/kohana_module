<?php
// head: doctype, head tag, title, meta, css, etc
echo View::factory('base/head')
	->set($kohana_view_data); ?>
<body class="<?php echo HTML::chars(trim($body_class)); ?>">
<?php
// development_message: message regarding site being in development; check for development inside the file
echo View::factory('base/development_message')
	->set($kohana_view_data);
// header: menus, logos, etc
echo View::factory('base/header')
	->set($kohana_view_data);
// wrapper: the content portion of the body including messages
echo View::factory('base/wrapper')
	->set($kohana_view_data);
// analytics: code for website stats, such as Google Analytics
echo View::factory('base/analytics')
	->set($kohana_view_data);
// footer_javascript: js to be loaded at the bottom of the page
echo View::factory('base/footer_javascript')
	->set($kohana_view_data);
// debug: debug output; detects debug mode within view
echo View::factory('base/debug')
		->set($kohana_view_data); ?>
</body>
</html>