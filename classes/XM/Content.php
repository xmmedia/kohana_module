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
	 * If it's a text only item, no link to edit will be displayed, but draft content maybe shown.
	 *
	 * @param   string  $code  The code for the content item.
	 * @return  string
	 */
	public static function display($code) {
		$content_item = ORM::factory('Content')
			->where('code', '=', $code)
			->find();
		if ($content_item->loaded()) {
			if (Auth::instance()->logged_in() && Content::allowed($content_item->code)) {
				if (Arr::get($_REQUEST, 'content_admin_show') == $content_item->code && ! $content_item->text_only_flag) {
					$highlight_content_item = TRUE;
					$html = '<div class="content_admin_show">';
				} else {
					$highlight_content_item = FALSE;
					$html = '';
				}

				if (Arr::get($_REQUEST, 'draft', FALSE) && $content_item->has_draft()) {
					$html .= Content::content($content_item->get_draft());
					$showing_draft = TRUE;
				} else {
					$html .= Content::content($content_item);
					$showing_draft = FALSE;
				}

				if ( ! $content_item->text_only_flag) {
					$html .= '<div class="content_admin_edit_links">' . HTML::anchor(Route::get('content_admin')->uri(array('action' => 'edit', 'id' => $content_item->id)) . '?popup=1', HTML::icon('edit') . 'Edit', array('class' => 'content_admin_edit js_content_admin_edit'))
						. '<div class="last_update">Last Update: ' . HTML::chars($content_item->last_update()) . '</div>';
				}

				if ($content_item->has_draft() && ! $content_item->text_only_flag) {
					if ($showing_draft) {
						$html .= '<div class="draft"><em><strong>Showing Waiting Draft</strong></em></div>';
					} else {
						$html .= '<div class="draft"><em>Has Waiting Draft</em></div>';
					}
				}

				if ( ! $content_item->text_only_flag) {
					// close the links div
					$html .= '</div>';
				}

				if ($highlight_content_item && ! $content_item->text_only_flag) {
					// close the content_admin_show div
					$html .= '</div>';
				}
			} else {
				$html = Content::content($content_item);
			}

			return $html;
		} else {
			throw new Kohana_Exception('The content item :code cannot be found', array(':code' => $code));
		}
	} // function display

	/**
	 * Returns the content for the content item, doing any additional processing that's needed.
	 * Right now, this check for merge tags. The allowed formats are:
	 *
	 *     *|MERGE_TAG|*
	 *     *|MERGE_TAG:value|*
	 *     *|MERGE_TAG:value|other data|*
	 *
	 * If multiple of the same (exact other than case) merge tags are found, the first return will be used for all of them.
	 * The merge tag will be converted into a method name, ie, `*|EMAIL_HIDE|*` will call `Content::_merge_email_hide()`.
	 * The full content after all previous replacements have been completed and the merge tag
	 * minus the deliminators will be sent to the merge method.
	 * The merge method should only return what replaces the merge tag.
	 *
	 * @param  Model_Content_Item  $content_item  The Content Item model.
	 * @return  string
	 */
	protected static function content($content_item) {
		// check to see if we have an opening and closing merge "tag" within the content
		if (strpos($content_item->content, '*|') !== FALSE && strpos($content_item->content, '|*') !== FALSE) {
			$content = $content_item->content;

			// find all the merge tags within the content
			$regex = '/\*\|(.+?)\|\*/misu';
			$tag_count = preg_match_all($regex, $content, $merge_tags);
			if ($tag_count > 0) {
				// loop through the merge tags
				foreach ($merge_tags[1] as $merge_tag) {
					$pipe_pos = strpos($merge_tag, '|');
					$colon_pos = strpos($merge_tag, ':');
					$merge_tag_name_end = $colon_pos < $pipe_pos ? $colon_pos : $pipe_pos;
					$merge_tag_name = substr($merge_tag, 0, $merge_tag_name_end);

					// run the method for the merge tag
					$method = '_merge_' . strtolower($merge_tag_name);
					$return = Content::$method($content, $merge_tag);

					$content = UTF8::str_ireplace('*|' . $merge_tag . '|*', $return, $content);
				}
			}

			return $content;

		} else {
			return $content_item->content;
		}
	} // function content

	/**
	 * Returns TRUE if the user is allowed to edit the content item.
	 *
	 * Required permission: content_admin
	 *
	 * Required either of: content_admin/* or content_admin/[code]
	 *
	 * @param   string  $code  The code for the content item.
	 * @return  boolean
	 */
	public static function allowed($code) {
		return (Auth::instance()->allowed('content_admin') && (Auth::instance()->allowed('content_admin/*') || Auth::instance()->allowed('content_admin/' . $code)));
	}
}