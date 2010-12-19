<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Database_MySQL extends Kohana_Database_MySQL {
	public $last_query_time;

	public function query($type, $sql, $as_object) {
		// Make sure the database is connected
		$this->_connection or $this->connect();

		if ( ! empty($this->_config['profiling'])) {
			// Benchmark this query for the current instance
			$benchmark = Profiler::start("Database ({$this->_instance})", $sql);
		}

		// record the time the query started at
		$query_start_time = microtime(TRUE);

		if ( ! empty($this->_config['connection']['persistent']) AND $this->_config['connection']['database'] !== Database_MySQL::$_current_databases[$this->_connection_id]) {
			// Select database on persistent connections
			$this->_select_db($this->_config['connection']['database']);
		}

		// Execute the query
		if (($result = mysql_query($sql, $this->_connection)) === FALSE) {
			if (isset($benchmark)) {
				// This benchmark is worthless
				Profiler::delete($benchmark);
			}

			throw new Database_Exception(':error [ :query ]',
				array(':error' => mysql_error($this->_connection), ':query' => $sql),
				mysql_errno($this->_connection));
		}

		// get the total time it took to run the query
		$this->last_query_time = microtime(TRUE) - $query_start_time;

		if (isset($benchmark)) {
			Profiler::stop($benchmark);
		}

		// Set the last query
		$this->last_query = $sql;

		if ($type === Database::SELECT) {
			// Return an iterator of results
			return new Database_MySQL_Result($result, $sql, $as_object);
		} elseif ($type === Database::INSERT) {
			// Return a list of insert id and rows created
			return array(
				mysql_insert_id($this->_connection),
				mysql_affected_rows($this->_connection),
			);
		} else {
			// Return the number of rows affected
			return mysql_affected_rows($this->_connection);
		}
	} // function query
} // class XM_Database_MySQL