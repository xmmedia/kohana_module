<?php defined('SYSPATH') or die ('No direct script access.');

return array(
	'private' => array(
		'class' => 'left',
		'items' => array(
			'Home' => array(
				'uri' => '/',
				'class' => 'home',
				'order' => 100,
			),

			'Admin' => array(
				'route' => 'xm_db_admin',
				'class' => 'admin',
				'logged_in_only' => TRUE,
				'order' => 200,

				'sub_menu' => array(
					'items' => array(
						'User Admin' => array(
							'route' => 'user_admin',
							'perm' => 'user_admin/index',
							'class' => 'user_admin',
						),
						'Groups / Permissions' => array(
							'route' => 'user_admin',
							'params' => array('action' => 'groups'),
							'perm' => 'user_admin/group/index',
							'class' => 'user_admin_groups',
						),
						'Content Admin' => array(
							'route' => 'content_admin',
							'perm' => 'content_admin',
							'class' => 'content_admin',
						),
						'DB Admin' => array(
							'route' => 'xm_db_admin',
							'perm' => 'xm_db_admin',
							'class' => 'xm_db_admin',
						),
						'Kohana User Guide' => array(
							'route' => 'docs/guide',
							'perm' => (XM::is_dev() && Auth::instance()->allowed('userguide')),
							'class' => 'user_guide',
						),
						'Kohana API Browser' => array(
							'route' => 'docs/api',
							'perm' => (XM::is_dev() && Auth::instance()->allowed('userguide')),
							'class' => 'user_guide_api',
						),
						'Model Create' => array(
							'route' => 'model_create',
							'perm' => (XM::is_dev() && Auth::instance()->allowed('xm_db_admin/model_create')),
							'class' => 'model_create',
						),
						'DB Change' => array(
							'route' => 'db_change',
							'perm' => 'db_change/index',
							'class' => 'db_change',
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
				'menu_replace' => array('Menu::users_name', array()),

				'sub_menu' => array(
					'class' => 'right',
					'items' => array(
						'My Account' => array(
							'route' => 'account',
							'params' => array('action' => 'profile'),
							'perm' => 'account/profile',
							'class' => 'account',
						),
						'Logout' => array(
							'route' => 'login',
							'params' => array('action' => 'logout'),
							'class' => 'logout',
						),
					),
				),
			),
		),
	),

);