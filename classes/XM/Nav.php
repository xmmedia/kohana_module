<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Generates a nav/menu in an unordered list.
 * Uses config/nav for the structure.
 * The structure can include permissions, CSS classes and the order.
 * See [https://github.com/xmmedia/kohana_module/wiki/Navigation] for more information.
 *
 * @package    XM
 * @category   Navigation
 * @author     XM Media Inc.
 * @copyright  (c) 2013 XM Media Inc.
 */
class XM_Nav {
	/**
	 * Stores the config for the navs.
	 * Populated the first time get() is called.
	 *
	 * @var  array
	 */
	public static $navs;

	/**
	 * Defaults that are merged with the values from the config for the main (first level) nav items.
	 *
	 * @var  array
	 */
	public static $main_nav_defaults = array(
		'class' => NULL,
		'items' => array(),
		'logged_in_only' => FALSE,
		'logged_out_only' => FALSE,
	);

	/**
	 * Defaults that are merged with the values from the config for each nav item that is not a main left.
	 *
	 * @var  array
	 */
	public static $nav_item_defaults = array(
		'uri' => NULL,
		'params' => NULL,
		'class' => NULL,
		'order' => 0,
		'logged_in_only' => FALSE,
		'logged_out_only' => FALSE,
		'perm' => TRUE,
	);

	/**
	 * The CSS class that is applied to <li>'s that have a sub nav.
	 *
	 * @var  string
	 */
	public static $has_sub_nav_css_class = 'has_subnav';

	/**
	 * The CSS class that is applied to <ul>'s that contain the sub nav for an <li>.
	 *
	 * @var  string
	 */
	public static $sub_nav_css_class = 'sub_nav';

	/**
	 * The HTML element that is used to show that there are more items below the navigation item.
	 * By default this becomes a downward arrow.
	 *
	 * @var  string
	 */
	public static $sub_nav_more_tag = '<span class="more"></span>';

	/**
	 * Generates the nav in a unordered list (<ul>) based on config/nav for the specified nav.
	 * If the nav is empty (no items), `NULL` will be returned.
	 *
	 * @param   string  $nav  The nav to generate.
	 *
	 * @return  string
	 */
	public static function get($nav) {
		if (Nav::$navs === NULL) {
			Nav::$navs = Kohana::$config->load('nav');
		}

		if ( ! isset(Nav::$navs[$nav])) {
			throw new Kohana_Exception('The nav could not be found ":nav"', array(':nav' => $nav));
		}

		$logged_in = Auth::instance()->logged_in();

		$nav_tags = array();

		// merge in the defaults
		Nav::$navs[$nav] += Nav::$main_nav_defaults;

		if ( ! empty(Nav::$navs[$nav]['items'])) {
			$nav_tags[] = '<ul' . HTML::attributes(array('class' => Nav::$navs[$nav]['class'])) . '>';

			$nav_items = array();
			foreach (Nav::$navs[$nav]['items'] as $nav_item => $item_details) {
				Nav::$navs[$nav]['items'][$nav_item] += Nav::$nav_item_defaults;

				$nav_items[$item_details['order']] = array(
					'label' => $nav_item,
					'item_details' => Nav::$navs[$nav]['items'][$nav_item],
				);
			}

			// sort the items
			ksort($nav_items);

			foreach ($nav_items as $nav_item) {
				$current_nav_tags = array();
				$sub_nav_tags = array();

				if ( ! Nav::allowed($logged_in, $nav_item['item_details'])) {
					continue;
				}

				$css_class = NULL;
				if ( ! empty($nav_item['item_details']['class'])) {
					$css_class .= $nav_item['item_details']['class'];
				}
				if ( ! empty($nav_item['item_details']['sub_menu']['items'])) {
					$has_subnav = TRUE;
					$css_class .= ( ! empty($css_class) ? ' ' : '') . Nav::$has_sub_nav_css_class;
				} else {
					$has_subnav = FALSE;
				}

				$current_nav_tags[] = '<li' . HTML::attributes(array('class' => $css_class)) . '>';

				$label = $nav_item['label'];
				if (isset($nav_item['item_details']['menu_replace'])) {
					$label = Nav::get_replace_label($nav_item['item_details']['menu_replace']);
				}
				$current_nav_tags[] = HTML::anchor(Nav::uri($nav_item['item_details']), $label . ($has_subnav ? Nav::$sub_nav_more_tag : ''));

				if ($has_subnav) {
					$css_class = Nav::$sub_nav_css_class;
					if ( ! empty($nav_item['item_details']['class'])) {
						$css_class .= ' ' . $nav_item['item_details']['class'];
					}

					$sub_nav_tags[] = '<ul' . HTML::attributes(array('class' => $css_class)) . '>';

					$sub_nav_items = array();
					foreach ($nav_item['item_details']['sub_menu']['items'] as $_item_name => $_item_details) {
						$_item_details += Nav::$nav_item_defaults;

						$sub_nav_items[$_item_details['order']] = array(
							'label' => $_item_name,
							'item_details' => $_item_details,
						);
					}

					ksort($sub_nav_items);

					foreach ($sub_nav_items as $_item_details) {
						if ( ! Nav::allowed($logged_in, $_item_details['item_details'])) {
							continue;
						}

						$css_class = NULL;
						if ( ! empty($_item_details['item_details']['class'])) {
							$css_class .= $_item_details['item_details']['class'];
						}

						$sub_nav_tags[] = '<li' . HTML::attributes(array('class' => $css_class)) . '>';

						$_label = $_item_details['label'];
						if (isset($_item_details['item_details']['menu_replace'])) {
							$_label = Nav::get_replace_label($_item_details['item_details']['menu_replace']);
						}
						$sub_nav_tags[] = HTML::anchor(Nav::uri($_item_details['item_details']), $_label);

						$sub_nav_tags[] = '</li>'; // sub nav item
					}

					$sub_nav_tags[] = '</ul>'; // sub nav list
				} // if has sub nav

				// if there is *no* sub nav or there is, then merge in the current nav & sub nav
				// (although the sub nav will be empty in the first case)
				// check if there more than 2 tags in sub nav, because it always creates the <ul> and </ul>
				if ( ! $has_subnav || count($sub_nav_tags) > 2) {
					$nav_tags = array_merge($nav_tags, $current_nav_tags, $sub_nav_tags);
					$nav_tags[] = '</li>'; // main nav item
				}
			} // foreach nav items

			$nav_tags[] = '</ul>'; // entire nav list
		}

		// only return a string when there is a nav to be displayed
		if (count($nav_tags) > 2) {
			return implode('', $nav_tags);
		} else {
			return NULL;
		}
	}

	/**
	 * Checks if the user is allowed to see the nav item.
	 * TRUE if allowed, FALSE if not allowed.
	 *
	 * @param   boolean  $logged_in     If the user is logged in.
	 * @param   array    $item_details  The nav item details, including logged_in_only, logged_out_only and perm keys.
	 *
	 * @return  boolean
	 */
	public static function allowed($logged_in, $item_details) {
		// check to see if they're required to be logged in or logged out to see the menu/menu item
		if (($item_details['logged_in_only'] && ! $logged_in) || ($item_details['logged_out_only'] && $logged_in)) {
			return FALSE;
		}

		// check to see if there is a required perm & if they have it to see the menu/menu item
		if ( ! $item_details['perm'] || (is_string($item_details['perm']) && ! Auth::instance()->allowed($item_details['perm']))) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Returns the URI based on the route or uri keys.
	 *
	 * @param   array  $item_details  The items details, includ the route, params and uri keys.
	 *
	 * @return  string
	 */
	public static function uri($item_details) {
		if (isset($item_details['route'])) {
			return Route::get($item_details['route'])->uri($item_details['params']);
		} else {
			return $item_details['uri'];
		}
	}

	/**
	 * If a nav item has the key 'menu_replace', the value of that key is passed to this method
	 * to generate the label for the nav item.
	 * Returns the new label.
	 *
	 * @param   string/array  $nav_replace  The function to call in a variety of formats.
	 *
	 * @return  string
	 */
	public static function get_replace_label($nav_replace) {
		list($method, $params) = $nav_replace;
		if ( ! is_string($method)) {
			// This is a lambda function
			$label = call_user_func_array($method, $params);

		} else if (method_exists('Menu', $method)) {
			$label = Nav::$method($params);

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

	/**
	 * Uses along with get_replace_label() to generate a nav item label
	 * that is the first and last name of the user.
	 * The value is HTML escaped.
	 * Grabs the user from the Auth object.
	 *
	 * @return  string
	 */
	public static function users_name() {
		$user = Auth::instance()->get_user();
		return HTML::chars($user->first_name . ' ' . $user->last_name);
	}
}