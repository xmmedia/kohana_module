<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Database_MySQL extends Kohana_Database_MySQL {
	/**
	 * Returns the current connection resource.
	 *
	 * @return  The MySQL connection resource.
	 */
	public function connection() {
		// Make sure the database is connected
		$this->_connection or $this->connect();

		return $this->_connection;
	}

	/**
	* Changes the current database
	*
	* @param  string  $database  The new database name
	*/
	public function select_db($database) {
		// Make sure the database is connected
		$this->_connection or $this->connect();

		$this->_select_db($database);
	}

	/**
	* Checks and optimizes the passed or all tables in the database.
	*
	* @param  array  $tables  The tables to optimize or skip to optimize all tables.
	*/
	public function optimize_tables(array $tables = NULL) {
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ($tables === NULL) {
			$tables = $this->list_tables();
		}

		foreach ($tables as $table_name) {
			$check_result = $this->query(Database::SELECT, "CHECK TABLE " . $this->quote_column($table_name))->current();
			if ($check_result['Msg_type'] == 'Error') {
				throw new Kohana_Exception('CHECK TABLE returned an error for table ' . $table_name . ': ' . $check_result['Msg_text']);
			}
		}

		$optimize_sql = "OPTIMIZE TABLE";
		foreach ($tables as $table_name) {
			$optimize_sql .= ' ' . $this->quote_column($table_name) . ',';
		}
		$optimize_sql = trim($optimize_sql, ',');
		$optimize_result = $this->query(Database::SELECT, $optimize_sql)->current();
		if ($optimize_result['Msg_type'] == 'Error') {
			throw new Kohana_Exception('OPTIMIZE TABLE returned an error : ' . $optimize_result['Msg_text']);
		}
	} // function optimize_tables
} // class Database_XM_MySQL