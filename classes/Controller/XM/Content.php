<?php defined('SYSPATH') or die ('No direct script access.');

/**
 * Controller for the admin of the content items.
 * Deals with displaying the list, edit form, history list, and reverting.
 * Also adds the required history records.
 * The permission "content_admin" is required for all actions.
 *
 * @package    XM
 * @category   Content Admin
 * @author     XM Media Inc.
 * @copyright  (c) 2012 XM Media Inc.
 */
class Controller_XM_Content extends Controller_Private {
	public $auth_required = TRUE;

	public $secure_actions = array(
		'index' => 'content_admin',
		'edit' => 'content_admin',
		'cancel' => 'content_admin',
		'discard_draft' => 'content_admin',
		'history' => 'content_admin',
		'view_changes' => 'content_admin',
		'history_view' => 'content_admin',
		'restore' => 'content_admin',
	);

	public $page = 'content_admin';

	protected $no_auto_render_actions = array('history_view');

	/**
	* Runs before the action.
	* Calls parent::before().
	* Adds the content admin CSS and JS along with TinyMCE.
	*
	* @return  void
	*/
	public function before() {
		parent::before();

		if ($this->auto_render) {
			$this->add_style('content_admin', 'xm/css/content.css');

			$this->add_script('tinymce_jquery', 'js/tinymce/jquery.tinymce.min.js')
				->add_script('tinymce', 'js/tinymce/tinymce.min.js')
				->add_script('tinymce_config', 'js/tinymce_config.min.js')
				->add_script('content_admin', 'xm/js/content.js');
		}
	} // function before

	/**
	 * Displays a list of the editable items along with links to edit, show on page, view history and view draft.
	 *
	 * @return void
	 */
	public function action_index() {
		$content_table = new HTMLTable(array(
			'heading' => array(
				'',
				'Name',
				'Waiting Draft',
				'Description',
				'Last Update',
			),
			'table_attributes' => array(
				'class' => 'cl4_content content',
			),
		));

		$content_items = ORM::factory('Content')
			->find_all();
		$route = Route::get('content_admin');
		foreach ($content_items as $content_item) {
			if (Content::allowed($content_item->code)) {
				$links = HTML::anchor($route->uri(array('action' => 'edit', 'id' => $content_item->id)), '<span class="cl4_icon cl4_edit"></span>', array('title' => 'Edit'));
				if ( ! empty($content_item->content_page_id) && ! empty($content_item->content_page->url)) {
					$links .= HTML::anchor($content_item->content_page->url . '?content_admin_show=' . $content_item->code, '<span class="cl4_icon cl4_view"></span>', array('title' => 'View', 'target' => '_blank'));
				} else {
					$links .= '<span class="cl4_icon"></span>';
				}
				$links .= HTML::anchor($route->uri(array('action' => 'history', 'id' => $content_item->id)), '<span class="cl4_icon cl4_info"></span>', array('title' => 'View History'));

				if ($content_item->has_draft()) {
					$draft_links = HTML::anchor($content_item->content_page->url . '?content_admin_show=' . $content_item->code . '&draft=1', '<span class="cl4_icon cl4_checked"></span>', array('title' => 'View the Draft', 'target' => '_blank'))
						. HTML::anchor($route->uri(array('action' => 'discard_draft', 'id' => $content_item->id)), '<span class="cl4_icon cl4_delete"></span>', array('title' => 'Discard the Draft'));
				} else {
					$draft_links = '';
				}

				$content_table->add_row(array(
					$links,
					HTML::chars($content_item->name),
					$draft_links,
					HTML::chars($content_item->description),
					HTML::chars($content_item->last_update()),
				));
			}
		}

		$this->template->page_title = 'Content Admin - ' . $this->page_title_append;
		$this->template->body_html .= View::factory('content_admin/index')
			->set('content_html', $content_table->get_html());
	} // function action_index

	/**
	 * Display an edit form for a record or update (save) an existing record.
	 * Used for the edit popup from the website when the popup parameter equals 1.
	 * User must have permission to edit the content item to access this action.
	 *
	 * @return void
	 */
	public function action_edit() {
		$popup = (bool) Arr::get($_REQUEST, 'popup');

		$content_item = ORM::factory('Content', $this->request->param('id'))
			->set_mode('edit');
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			Message::add('You don\'t have permission to edit this item.', Message::$error);
			$this->redirect_to_index();
		}

		// load some extra CSS and onload JS if in a popup
		if ($popup) {
			$this->add_style('content_admin_popup', 'xm/css/content_popup.css');
			$this->add_on_load_js('$(\'.content_admin_cancel\').on(\'click\', function() {
window.close();
});');
		}

		// save if post is not empty
		if ( ! empty($_POST)) {
			try {
				$content = Arr::path($_POST, 'c_record.content.0.content');
				// check to see if any changes were made
				if ($content !== NULL && ($content !== $content_item->content || $content_item->has_draft())) {
					$immediately_live = (bool) Arr::get($_POST, 'immediately_live');

					// store the content in the model, even though we may not save it (depending on immediately_live)
					$content_item->content = $content;

					// make all existing records in the content history table as history if they haven't already
					$existing_content_history = $content_item->content_history
						->where('history_date', '=', 0)
						->find_all();
					foreach ($existing_content_history as $content_history) {
						$content_history->values(array(
								'history_date' => DB::expr("NOW()"),
								'history_user_id' => Auth::instance()->get_user()->pk(),
							))
							->save();
					}

					$content_history = ORM::factory('Content_History')
						->values(array(
							'content_id' => $content_item->id,
							'creation_user_id' => Auth::instance()->get_user()->pk(),
							'content' => $content_item->content,
							'comments' => Arr::path($_POST, 'c_record.content_history.0.comments', ''),
						));
					// only set the post date and user when we are going live with this immediately
					if ($immediately_live) {
						$content_history->values(array(
								'post_date' => DB::expr("NOW()"),
								'post_user_id' => Auth::instance()->get_user()->pk(),
							));
					}
					$content_history->save();

					// if making it live immediately, update the last update fields
					if ($immediately_live) {
						$content_item->values(array(
								'last_update' => DB::expr("NOW()"),
								'last_update_user_id' => Auth::instance()->get_user()->pk(),
							))
							->save();
					}

					if ( ! $immediately_live) {
						Message::add('The content for "' . HTML::chars($content_item->name) . '" was saved as a draft. To activated, edit the content again.', Message::$warning);
					} else {
						Message::add('The content "' . HTML::chars($content_item->name) . '" was successfully updated.', Message::$notice);
					}

				// no changes made
				} else {
					Message::add('No changes were made to the content "' . HTML::chars($content_item->name) . '" so no changes were record.', Message::$notice);
				} // if

				// if it's a popup, refresh the parent page and close the window after 2s
				if ($popup) {
					$this->template->body_html .= '<script>
setTimeout("window.opener.location.href = window.opener.location.href;", 500);
setTimeout("window.close();", 2000);
</script>';
					return;
				} else {
					$this->redirect_to_index();
				}
			} catch (ORM_Validation_Exception $e) {
				Message::add('Please correct the following before submitting: ' . Message::add_validation_errors($e, ''), Message::$error);
			}

		// there is a draft, so put the draft's content into the content item
		} else if ($content_item->has_draft()) {
			$draft = $content_item->get_draft();
			$content_item->content = $draft->content;
		} // if

		$this->template->page_title = 'Edit - ' . $content_item->name . ' - Content Admin - ' . $this->page_title_append;
		$this->template->body_html .= View::factory('content_admin/edit')
			->set('form_open', Form::open($this->request->uri()))
			->bind('popup', $popup)
			->bind('content_item', $content_item)
			->set('content_history', ORM::factory('Content_History'));
	} // function action_edit

	/**
	 * Discards (expires) the waiting draft for the current content item.
	 * User must have permission to edit the content item to access this action.
	 *
	 * @return void
	 */
	public function action_discard_draft() {
		$content_item = ORM::factory('Content', $this->request->param('id'))
			->set_mode('edit');
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			Message::add('You don\'t have permission to edit this item.', Message::$error);
			$this->redirect_to_index();
		}

		if ( ! $content_item->has_draft()) {
			Message::add('The selected contnet item no longer has a draft.', Message::$error);
			$this->redirect_to_index();
		}

		$content_item->get_draft()
			->delete();

		Message::add('The draft for "' . HTML::chars($content_item->name) . '" was discarded.', Message::$notice);
		$this->redirect_to_index();
	} // function action_discard_draft

	/**
	 * Displays the history/changes for the select content item.
	 * Includes links to view changes, view a specific version and restore to a specific version.
	 * User must have permission to edit the content item to access this action.
	 *
	 * @return void
	 */
	public function action_history() {
		$content_item = ORM::factory('Content', $this->request->param('id'));
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			Message::add('You don\'t have permission to view the history for this item.', Message::$error);
			$this->redirect_to_index();
		}

		$content_history_table = new HTMLTable(array(
			'heading' => array(
				'',
				'Date',
				'User',
				'Comments',
			),
			'table_attributes' => array(
				'class' => 'cl4_content content_history js_content_history',
			),
		));

		$content_history = $content_item->content_history
			->find_all();
		$found_live_version = FALSE;
		foreach ($content_history as $history) {
			if ( ! $found_live_version && ! Form::check_date_empty_value($history->post_date)) {
				$is_live_html = ' <span class="cl4_icon cl4_checked" title="This is the live version"></span>';
				$found_live_version = TRUE;
			} else {
				$is_live_html = '';
			}

			$content_history_table->add_row(array(
				HTML::anchor(Route::get('content_admin')->uri(array('action' => 'view_changes', 'id' => $history->id)), '<span class="cl4_icon cl4_info"></span>', array('title' => 'View Changes'))
					. HTML::anchor(Route::get('content_admin')->uri(array('action' => 'history_view', 'id' => $history->id)), '<span class="cl4_icon cl4_view"></span>', array('class' => 'js_history_view', 'title' => 'View this Version'))
					. ( ! empty($is_live_html) ? HTML::anchor(Route::get('content_admin')->uri(array('action' => 'restore', 'id' => $history->id)), '<span class="cl4_icon cl4_refresh"></span>', array('class' => 'js_restore', 'title' => 'Restore this Version')) : '<span class="cl4_icon"></span>'),
				HTML::chars($history->creation_date) . $is_live_html,
				HTML::chars($history->creation_user->name()),
				HTML::chars($history->comments),
			));
		} // foreach

		$this->template->page_title = 'History - ' . $content_item->name . ' - Content Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('content_admin/history')
			->bind('content_item', $content_item)
			->set('content_history_html', $content_history_table->get_html());
	} // function action_history

	/**
	 * Displays the diff between 2 versions of content, by default the previous version.
	 * User must have permission to edit the content item to access this action.
	 * Uses daisydiff and creates 3 temporary files for the diff generation.
	 * Includes a drop down for the user to select a different version to compare to.
	 *
	 * @return void
	 */
	public function action_view_changes() {
		$content_history = ORM::factory('Content_History', $this->request->param('id'));
		if ( ! $content_history->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		$content_item = $content_history->content_item;
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			Message::add('You don\'t have permission to view the changes
			 for this item.', Message::$error);
			$this->redirect_to_index();
		}

		$compare_to = Arr::get($_REQUEST, 'compare_to');
		if ( ! empty($compare_to)) {
			$prev_content_history = ORM::factory('Content_History', $compare_to);
		} else {
			$prev_content_history = ORM::factory('Content_History')
				->where('content_id', '=', $content_item->id)
				->where('creation_date', '<', $content_history->creation_date)
				->limit(1)
				->find();
		}
		if ( ! $prev_content_history->loaded()) {
			Message::add('No previous changes could be found, therefore nothing to compare the content to. Please pick a history item that is not the last one.', Message::$error);
			$this->redirect(Route::get('content_admin')->uri(array('action' => 'history', 'id' => $content_item->id)));
		}

		// generate the diff using daisydiff (http://code.google.com/p/daisydiff/)
		// create a temporary file for the "old" and the "new" version
		$tmp_old_path = tempnam('', 'diff_old_');
		$tmp_old = fopen($tmp_old_path, 'w+');
		fwrite($tmp_old, $prev_content_history->content);
		fclose($tmp_old);

		$tmp_new_path = tempnam('', 'diff_new_');
		$tmp_new = fopen($tmp_new_path, 'w+');
		fwrite($tmp_new, $content_history->content);
		fclose($tmp_new);

		$tmp_xml_path = tempnam('', 'diff_xml_');

		// execute a shell command to generate the diff XML
		$cmd = 'java -jar ' . MODPATH . 'xm/vendor/daisydiff.jar ' . $tmp_old_path . ' ' . $tmp_new_path . ' --output=xml --file=' . $tmp_xml_path . ' --q && cat ' . $tmp_xml_path;
		$daisy_xml = shell_exec($cmd);

		// remove the first bit of the XML diff
		$diff = substr($daisy_xml, strpos($daisy_xml, '<diffreport><css/><diff>'));
		// remove the last bit of the XML diff
		$diff = substr($diff, 0, strpos($diff, '</diff></diffreport>'));

		// delete all temp the files
		unlink($tmp_old_path);
		unlink($tmp_new_path);
		unlink($tmp_xml_path);

		// create the drop down to select a different version to compare to
		$_content_history = $content_item->content_history
			->find_all();
		$content_history_array = array();
		foreach ($_content_history as $history) {
			$content_history_array[$history->id] = $history->creation_date . ' by ' . $history->creation_user->name();
		}
		$history_select = Form::select('compare_to', $content_history_array, $prev_content_history->id, array('id' => 'compare_to', 'class' => 'js_compare_to'));

		$this->template->page_title = 'View Changes - ' . $content_item->name . ' - Content Admin - ' . $this->page_title_append;
		$this->template->body_html = View::factory('content_admin/diff')
			->bind('content_item', $content_item)
			->bind('content_history', $content_history)
			->bind('prev_content_history', $prev_content_history)
			->bind('history_select', $history_select)
			->bind('diff', $diff);
	} // function action_view_changes

	/**
	 * AJAX: Displays the content of a specific history item.
	 * User must have permission to edit the content item to access this action.
	 *
	 * @return void
	 */
	public function action_history_view() {
		$content_history = ORM::factory('Content_History', $this->request->param('id'));
		if ( ! $content_history->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		$content_item = $content_history->content_item;
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			AJAX_Status::echo_json(AJAX_Status::ajax(array(
				'html' => 'You don\'t have permission to view this history item.',
			)));
		}

		AJAX_Status::echo_json(AJAX_Status::ajax(array(
			'html' => $content_history->content,
		)));
	} // function action_history_view

	/**
	 * Restores a previous version.
	 * Restores by loading the previous version into the current content item, marking all existing history records
	 * as history and then creating a new history item for the "new" version.
	 * User must have permission to edit the content item to access this action.
	 *
	 * @return void
	 */
	public function action_restore() {
		$content_history = ORM::factory('Content_History', $this->request->param('id'));
		if ( ! $content_history->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		$content_item = $content_history->content_item;
		if ( ! $content_item->loaded()) {
			throw new Kohana_Exception('The content history item could not be loaded');
		}

		if ( ! Content::allowed($content_item->code)) {
			AJAX_Status::echo_json(AJAX_Status::ajax(array(
				'html' => 'You don\'t have permission to view this history item.',
			)));
		}

		// store the content in the model, even though we may not save it (depending on immediately_live)
		$content = $content_item->content;
		$history_content = $content_history->content;

		if ($content !== $history_content) {
			// make all existing records in the content history table as history if they haven't already
			$existing_content_history = $content_item->content_history
				->where('history_date', '=', 0)
				->find_all();
			foreach ($existing_content_history as $content_history) {
				$content_history->values(array(
						'history_date' => DB::expr("NOW()"),
						'history_user_id' => Auth::instance()->get_user()->pk(),
					))
					->save();
			}

			$content_history = ORM::factory('Content_History')
				->values(array(
					'content_id' => $content_item->id,
					'creation_user_id' => Auth::instance()->get_user()->pk(),
					'content' => $content_item->content,
					'post_date' => DB::expr("NOW()"),
					'post_user_id' => Auth::instance()->get_user()->pk(),
				))
				->save();

			$content_item->values(array(
					'content' => $history_content,
					'last_update' => DB::expr("NOW()"),
					'last_update_user_id' => Auth::instance()->get_user()->pk(),
				))
				->save();

			Message::add('The content "' . HTML::chars($content_item->name) . '" was successfully updated.', Message::$notice);

		} else {
			Message::add('Both the history item and the current content are exactly the same so no changes were made.', Message::$warning);
		}

		$this->redirect_to_index();
	} // function action_restore

	/**
	 * Cancel the current action by redirecting back to the index action.
	 *
	 * @return  void
	 */
	public function action_cancel() {
		// add a notice to be displayed
		Message::message('cl4admin', 'action_cancelled', NULL, Message::$notice);
		// redirect to the index
		$this->redirect_to_index();
	}

	/**
	 * Redirects the user to the index.
	 *
	 * @return  void
	 */
	function redirect_to_index() {
		$this->redirect(Route::get('content_admin')->uri());
	}
}