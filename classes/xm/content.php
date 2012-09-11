<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Contains the common code for display and editing the content items.
 *
 * @package    XM
 * @category   Content Admin
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class XM_Content {
	/**
	 * Returns the content of the content item (usually HTML) to display the content item.
	 * Also checks to see if the user is logged in and has permissions, then displays the edit link,
	 * last update and if there's a waiting draft.
	 * Throws and exception if the content item cannnot be found.
	 *
	 * @param   string  $code  The code for the content item.
	 * @return  string
	 */
	public static function display($code) {
		$content_item = ORM::factory('content')
			->where('code', '=', $code)
			->find();
		if ($content_item->loaded()) {
			if (Auth::instance()->logged_in() && Content::allowed($content_item->code)) {
				if (Arr::get($_REQUEST, 'content_admin_show') == $content_item->code) {
					$highlight_content_item = TRUE;
					$html = '<div class="contentadmin_show">';
				} else {
					$highlight_content_item = FALSE;
					$html = '';
				}

				if (Arr::get($_REQUEST, 'draft', FALSE) && $content_item->has_draft()) {
					$html .= $content_item->get_draft()->content;
					$showing_draft = TRUE;
				} else {
					$html .= $content_item->content;
					$showing_draft = FALSE;
				}

				$html .= '<div class="contentadmin_edit_links">' . HTML::anchor(Route::get('content_admin')->uri(array('action' => 'edit', 'id' => $content_item->id)) . '?popup=1', '&nbsp;Edit', array('class' => 'cl4_edit contentadmin_edit'))
					. '<div class="last_update">Last Update: ' . HTML::chars($content_item->last_update()) . '</div>';

				if ($content_item->has_draft()) {
					if ($showing_draft) {
						$html .= '<div class="draft"><em><strong>Showing Waiting Draft</strong></em></div>';
					} else {
						$html .= '<div class="draft"><em>Has Waiting Draft</em></div>';
					}
				}

				// close the links div
				$html .= '</div>';

				if ($highlight_content_item) {
					// close the contentadmin_show div
					$html .= '</div>';
				}
			} else {
				$html = $content_item->content;
			}

			return $html;
		} else {
			throw new Kohana_Exception('The content item :code cannot be found', array(':code' => $code));
		}
	} // function display

	/**
	 * Returns TRUE if the user is allowed to edit the content item.
	 *
	 * Required permission: contentadmin
	 *
	 * Required either of: contentadmin/* or contentadmin/[code]
	 *
	 * @param   string  $code  The code for the content item.
	 * @return  boolean
	 */
	public static function allowed($code) {
		return (Auth::instance()->allowed('contentadmin') && (Auth::instance()->allowed('contentadmin/*') || Auth::instance()->allowed('contentadmin/' . $code)));
	}
}