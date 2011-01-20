<?php defined('SYSPATH') or die ('No direct script access.');

class XM_Database_MySQL extends Kohana_Database_MySQL {
	/**
	* Changes the current database
	*
	* @param  string  $database  The new database name
	*/
	public function select_db($database) {
		$this->_select_db($database);
	}
} // class Database_XM_MySQL