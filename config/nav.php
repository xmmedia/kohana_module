<?php defined('SYSPATH') or die ('No direct script access.');

return array(
	// the navigation name
	'private' => array(
		// the class to apply to the <ul>
		'class' => 'left',
		// the items in the navigation
		'items' => array(
			// the key is the label that will appear in the menu
			'Home' => array(
				// either a uri or route can be specified
				'uri' => '/',
				// the class to apply to the <li> surrounding the link
				'class' => 'home',
				// the order of the menu items
				// these are reordered within the get() method
				'order' => 100,
			),

			'Admin' => array(
				// the route to use to generate the uri
				// if no params are set, NULL will be passed to Route::uri()
				'route' => 'xm_db_admin',
				'class' => 'admin',
				// only allow logged in users to see this menu item
				'logged_in_only' => TRUE,
				'order' => 200,

				// the menu items in the sub nav
				// if this doesn't exist or is empty, there will be no sub nav
				'sub_menu' => array(
					// the items in the sub nav
					'items' => array(
						'Content Admin' => array(
							'route' => 'content_admin',
							'perm' => 'content_admin',
							'class' => 'content_admin',
							'order' => 100,
						),
						'User Admin' => array(
							'route' => 'user_admin',
							'perm' => 'user_admin/index',
							'class' => 'user_admin',
							'order' => 200,
						),
						'Groups / Permissions' => array(
							// the route to use to generate the uri
							'route' => 'user_admin',
							// the params to pass to the route
							'params' => array('action' => 'groups'),
							// the permission to use to check if the user has permission to the nav item
							'perm' => 'user_admin/group/index',
							'class' => 'user_admin_groups',
							'order' => 300,
						),
						'DB Admin' => array(
							'route' => 'xm_db_admin',
							'perm' => 'xm_db_admin',
							'class' => 'xm_db_admin',
							'order' => 400,
						),
						'Error Admin' => array(
							'route' => 'error_admin',
							// 'perm' => 'error_admin',
							'class' => 'error_admin',
							'order' => 500,
						),
						'Kohana User Guide' => array(
							'route' => 'docs/guide',
							// instead of a string, the 'perm' key can also be a boolean
							// in which case, TRUE will show the nav item and FALSE will hide it
							'perm' => (XM::is_dev() && Auth::instance()->allowed('userguide')),
							'class' => 'user_guide',
							'order' => 600,
						),
						'Kohana API Browser' => array(
							'route' => 'docs/api',
							'perm' => (XM::is_dev() && Auth::instance()->allowed('userguide')),
							'class' => 'user_guide_api',
							'order' => 700,
						),
						'Model Create' => array(
							'route' => 'model_create',
							'perm' => (XM::is_dev() && Auth::instance()->allowed('xm_db_admin/model_create')),
							'class' => 'model_create',
							'order' => 800,
						),
						'DB Change' => array(
							'route' => 'db_change',
							'perm' => 'db_change/index',
							'class' => 'db_change',
							'order' => 900,
						),
					),
				),
			),
		),
	),

	'private_right' => array(
		'class' => 'right',
		'items' => array(
			'My Account' => array(
				'route' => 'xm_db_admin',
				'class' => 'right user',
				'logged_in_only' => TRUE,
				'order' => 100,
				// if set, this method will be used to generate the nav item label
				// see Nav::get_replace_label() for the available options
				'menu_replace' => array('Nav::users_name', array()),

				'sub_menu' => array(
					'class' => 'right',
					'items' => array(
						'My Account' => array(
							'route' => 'account',
							'params' => array('action' => 'profile'),
							'perm' => 'account/profile',
							'class' => 'account',
							'order' => 100,
						),
						'Logout' => array(
							'route' => 'login',
							'params' => array('action' => 'logout'),
							'class' => 'logout',
							'order' => 200,
						),
					),
				),
			),

			'Login' => array(
				'route' => 'login',
				'class' => 'login',
				// will only show the nav item when the user is logged out
				'logged_out_only' => TRUE,
				'order' => 200,
			),
		),
	),

);