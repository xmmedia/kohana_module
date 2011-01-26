<?php defined('SYSPATH') or die ('No direct script access.');

class Controller_XM_DBChange extends Controller_Base {
	public $auth_required = TRUE;

	public $secure_actions = array(
		'index' => 'dbchange/index',
	);

	public function action_index() {
		$this->template->body_html = View::factory('dbchange/index')
			->bind('db_change_sql', $db_change_sql)
			->bind('db_checkboxes', $db_checkboxes);

		$mysql_username = Kohana::config('database.' . (string) Database::instance() . '.connection.username');

		$db_list = array();
		foreach (DB::query(Database::SELECT, "SHOW DATABASES;")->execute()->as_array(NULL, 'Database') as $_db) {
			// remove the information schema from the list of dbs as it's unlikely someone will want to update something in it
			if ($_db != 'information_schema') {
				$db_list[$_db] = $_db;
			}
		}

		if ( ! empty($_POST['db']) && is_array($_POST['db'])) {
			$selected_dbs = $_POST['db'];
		} else if ( ! empty($_POST)) {
			$selected_dbs = array();
		} else {
			$current_db = DB::select(array(DB::Expr('DATABASE()'), 'database'))->execute()->current();
			$selected_dbs = array($current_db['database'], $current_db['database']);
		}
		$db_checkboxes = Form::checkboxes('db[]', $db_list, $selected_dbs, array(), array(
			'checkbox_hidden' => FALSE,
			'orientation' => 'vertical',
		));

		$this->add_on_load_js(<<<EOA
$(function() {
	$('a.select_all').click(function() {
		$('input[name="db[]"]').check();
		return false;
	});
	$('a.select_none').click(function() {
		$('input[name="db[]"]').uncheck();
		return false;
	});
	$('input.clear').click(function() {
		$('a.select_none').click();
		$('textarea.db_change_sql').val('');
	});
});
EOA
);

		if ( ! empty($_POST)) {
			if (empty($_POST['sql'])) {
				Message::add('No SQL was received.', Message::$error);

			} else {
				$db_change_sql = $_POST['sql'];

				Kohana::load(Kohana::find_file('vendor', 'sqlparser/sqlparser.lib'));

				$parsed_queries = PMA_SQP_parse($db_change_sql);
				if (empty($parsed_queries)) {
					Message::add('No SQL was received or could be parsed.', Message::$error);
				} else if (empty($selected_dbs)) {
					Message::add('No databases were selected.', Message::$error);

				} else {
					foreach ($selected_dbs as $key => $use_db) {
						if ( ! isset($db_list[$use_db])) {
							Message::add('The passed DB name :db is no in the list of accessible databases and will be skipped', Message::$error, array(':db' => $use_db));
							unset($selected_dbs[$key]);
						}
					} // foreach

					$queries = array();
					$i = 0;
					foreach ($parsed_queries as $key => $sql_part) {
						if ($key === 'raw' || $key === 'len' || $sql_part['type'] === 'white_newline') {
							continue;
						}

						if ( ! isset($queries[$i])) {
							switch(strtoupper($sql_part['data'])) {
								case 'SELECT' :
									$type = Database::SELECT;
									break;
								case 'DELETE' :
									$type = Database::DELETE;
									break;
								case 'INSERT' :
									$type = Database::INSERT;
									break;
								case 'UPDATE' :
								case 'ALTER' :
									$type = Database::UPDATE;
									break;
								default :
									$type = NULL;
									break;
							}

							$queries[$i] = array(
								'type' => $type,
								'query' => '',
							);
						}

						if ($sql_part['type'] == 'punct_queryend') {
							$queries[$i]['query'] .= ';';
							++$i;
						} else {
							$queries[$i]['query'] .= ($queries[$i]['query'] != '' ? ' ' : '') . $sql_part['data'];
						}
					} // foreach

					foreach ($selected_dbs as $use_db) {
						try {
							DB::query(NULL, "USE " . Database::instance()->quote_identifier($use_db))->execute();
						} catch (Exception $e) {
							Message::add('Failed to select the database: ' . Kohana::exception_text($e), Message::$error);
							continue;
						}

						foreach ($queries as $query) {
							try {
								$result = Database::instance()->query($query['type'], $query['query']);
								$successful = TRUE;
							} catch (Exception $e) {
								$error = Kohana::exception_text($e);
								$successful = FALSE;
							}

							$type = strtoupper(substr($query['query'], 0, strpos($query['query'], ' ')));
							$message = $type . ' ' . $use_db . '<br><pre class="query">' . $query['query'];

							if ($successful) {
								switch ($type) {
									case 'INSERT' :
										$message .= ' -- ' . $result[1] . ' row' . Text::s($result[1]) . ' affected';
										break;
									case 'UPDATE' :
									case 'DELETE' :
									case 'ALTER' :
										$message .= ' -- ' . $result . ' row' . Text::s($result) . ' affected';
										break;
									case 'SELECT' :
										$message .= ' -- ' . count($result) . ' row' . Text::s(count($result)) . ' found';
										break;
								}
							} // if

							Message::add($message . '</pre>', ($successful ? Message::$notice : Message::$error));

							if ( ! $successful) {
								Message::add($error, Message::$error);
								break;
							}
						} // foreach
					} // foreach

					//Message::add(Kohana::debug($queries), Message::$notice);
				} // if
			} // if
		} // if
	} // function action_index
} // class Controller_DB_Change