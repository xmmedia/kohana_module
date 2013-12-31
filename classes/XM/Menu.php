<?php defined('SYSPATH') OR die('No direct access allowed.');

class XM_Menu {
	public static $menus;

	public static $main_menu_defaults = array(
		'class' => NULL,
		'items' => array(),
		'logged_in_only' => FALSE,
	);
	public static $menu_item_defaults = array(
		'uri' => NULL,
		'params' => NULL,
		'class' => NULL,
		'order' => 0,
		'logged_in_only' => FALSE,
		'perm' => TRUE,
	);

	public static $has_sub_nav_css_class = 'has_subnav';
	public static $sub_nav_css_class = 'sub_nav';
	public static $sub_nav_more_tag = '<span class="more"></span>';

	public static function get($menu) {
		if (Menu::$menus === NULL) {
			Menu::$menus = Kohana::$config->load('menu');
		}

		if ( ! isset(Menu::$menus[$menu])) {
			throw new Kohana_Exception('The menu could not be found ":menu"', array(':menu' => $menu));
		}

		$logged_in = Auth::instance()->logged_in();

		$menu_tags = array();

		// merge in the defaults
		Menu::$menus[$menu] += Menu::$main_menu_defaults;

		if ( ! empty(Menu::$menus[$menu]['items'])) {
			$menu_tags[] = '<ul' . HTML::attributes(array('class' => Menu::$menus[$menu]['class'])) . '>';

			$menu_items = array();

			foreach (Menu::$menus[$menu]['items'] as $menu_item => $item_details) {
				Menu::$menus[$menu]['items'][$menu_item] += Menu::$menu_item_defaults;

				$menu_items[$item_details['order']] = array(
					'label' => $menu_item,
					'item_details' => Menu::$menus[$menu]['items'][$menu_item],
				);
			}

			// sort the items
			ksort($menu_items);

			foreach ($menu_items as $menu_item) {
				// check to see if they're required to be logged in to see the menu/menu item
				if ($menu_item['item_details']['logged_in_only'] && ! $logged_in) {
					continue;
				}

				// check to see if there is a required perm & if they have it to see the menu/menu item
				if ( ! $menu_item['item_details']['perm'] || (is_string($menu_item['item_details']['perm']) && ! Auth::instance()->allowed($menu_item['item_details']['perm']))) {
					continue;
				}

				$css_class = NULL;
				if ( ! empty($menu_item['item_details']['class'])) {
					$css_class .= $menu_item['item_details']['class'];
				}
				if ( ! empty($menu_item['item_details']['sub_menu']['items'])) {
					$has_subnav = TRUE;
					$css_class .= ( ! empty($css_class) ? ' ' : '') . Menu::$has_sub_nav_css_class;
				} else {
					$has_subnav = FALSE;
				}

				$menu_tags[] = '<li' . HTML::attributes(array('class' => $css_class)) . '>';

				if (isset($menu_item['item_details']['route'])) {
					$uri = Route::get($menu_item['item_details']['route'])->uri($menu_item['item_details']['params']);
				} else {
					$uri = $menu_item['item_details']['uri'];
				}

				$label = $menu_item['label'];
				if (isset($menu_item['item_details']['menu_replace'])) {
					$label = Menu::get_replace_label($menu_item['item_details']['menu_replace']);
				}
				$menu_tags[] = HTML::anchor($uri, $label . ($has_subnav ? Menu::$sub_nav_more_tag : ''));

				if ($has_subnav) {
					$css_class = Menu::$sub_nav_css_class;
					if ( ! empty($menu_item['item_details']['class'])) {
						$css_class .= ' ' . $menu_item['item_details']['class'];
					}

					$menu_tags[] = '<ul' . HTML::attributes(array('class' => $css_class)) . '>';
					foreach ($menu_item['item_details']['sub_menu']['items'] as $_item_name => $_item_details) {
						$_item_details += Menu::$menu_item_defaults;

						// check to see if they're required to be logged in to see the menu/menu item
						if ($_item_details['logged_in_only'] && ! $logged_in) {
							continue;
						}

						// check to see if there is a required perm & if they have it to see the menu/menu item
						if ( ! $_item_details['perm'] || (is_string($_item_details['perm']) && ! Auth::instance()->allowed($_item_details['perm']))) {
							continue;
						}

						$css_class = NULL;
						if ( ! empty($_item_details['class'])) {
							$css_class .= $_item_details['class'];
						}

						$menu_tags[] = '<li' . HTML::attributes(array('class' => $css_class)) . '>';

						if (isset($_item_details['route'])) {
							$uri = Route::get($_item_details['route'])->uri($_item_details['params']);
						} else {
							$uri = $_item_details['uri'];
						}

						$_label = $_item_name;
						if (isset($menu_item['item_details']['menu_replace'])) {
							$_label = Menu::get_replace_label($menu_item['item_details']['menu_replace']);
						}
						$menu_tags[] = HTML::anchor($uri, $_label);

						$menu_tags[] = '</li>';
					}
					$menu_tags[] = '</ul>';
				}

				$menu_tags[] = '</li>';
			}

			$menu_tags[] = '</ul>';
		}

		return implode('', $menu_tags);
	}

	public static function get_replace_label($menu_replace) {
		list($method, $params) = $menu_replace;
		if ( ! is_string($method)) {
			// This is a lambda function
			$label = call_user_func_array($method, $params);

		} else if (method_exists('Menu', $method)) {
			$label = Menu::$method($params);

		} else if (strpos($method, '::') === FALSE) {
			// Use a function call
			$function = new ReflectionFunction($method);

			// Call $function($this[$field], $param, ...) with Reflection
			$label = $function->invokeArgs($params);

		} else {
			// Split the class and method of the rule
			list($class, $_method) = explode('::', $method, 2);

			// Use a static method call
			$_method = new ReflectionMethod($class, $_method);

			// Call $Class::$method($this[$field], $param, ...) with Reflection
			$label = $_method->invokeArgs(NULL, $params);
		}

		return $label;
	}

	public static function users_name() {
		$user = Auth::instance()->get_user();
		return HTML::chars($user->first_name . ' ' . $user->last_name);
	}
}