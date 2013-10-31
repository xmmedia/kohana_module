<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'model_comment' => array(
		'package' => 'Application',
		'category' => 'Models',
		'author' => 'XM Media Inc.',
		'copyright' => '(c) ' . date('Y') . ' XM Media Inc.',
	),

	// by default, model create will use the values in the xmorm config (key: default_meta_data)
	// this will override those values
	'default_meta_data' => array(
		'list_flag' => TRUE,      // displays the data for this column in get_list() and get_editable_list()
		'edit_flag' => TRUE,      // displays this field in any edit forms and allows the user to save new values
		'search_flag' => TRUE,    // displays this field in the search mode (search form)
		'view_flag' => TRUE,      // displays this field in the view mode
	),

	// these are field types that can have foreign values
	'relationship_field_types' => array('Select', 'Radios'),

	// add any fields that you want customize the default meta data for
	// setup the same way as the same key in the xmorm config
	'default_meta_data_field_type' => array(),

	// labels for columns where the labels can't easily be generated with ucwords()
	'special_labels' => array(
		'datetime' => 'Date Time',
		'html' => 'HTML',
		'id' => 'ID',
		'ip_address' => 'IP Address',
		'sql' => 'SQL',
		'url' => 'URL',
	),
);