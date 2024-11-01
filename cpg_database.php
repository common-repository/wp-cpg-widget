<?php

/*
    cpg_database.php
	
    The a class that represents and accesses the Coppermine database.

    Copyright 2009, Melanie Rhianna Lewis

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * CPG_Database class
 * 
 * @since 0.01
 * @author Melanie Rhianna Lewis
 */
class CPG_Database {
	
	/**
	 * The database server name
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_server;
	
	/**
	 * The database name
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_database;
	
	/**
	 * The user name
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_username;
	
	/**
	 * The user password
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_password;
	
	/**
	 * Whether to send errors to the output
	 * 
	 * @since 0.01
	 * @access var
	 * @var bool
	 */
	var $m_show_errors = false;
	
	/**
	 * Whether there is an error condition outstanding
	 * 
	 * @since 0.01
	 * @access var
	 * @var bool
	 */
	var $m_has_error = false;
	
	/**
	 * The last error that occurred
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_last_error = '';
	
	/**
	 * The database handle
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_dbh = false;
	
	/**
	 * True if connected to the database
	 * 
	 * @since 0.01
	 * @access var
	 * @var string
	 */
	var $m_connected = false;
	
	/**
	 * Constructs a new instance (PHP 4 version).
	 * 
	 * The constructor constructs a new instance of the class connecting
	 * to the database server with the specified values and selecting the
	 * specified database.  After construction is called hasError() 
	 * should be checked to see if connection was successful.
	 * 
	 * @since 0.01
	 * 
	 * @param string $server The name of the server.
	 * @param string $database The name of the database.
	 * @param string $username The user name.
	 * @param string $password The user password.
	 */
	function CPG_Database($server, $database, $username, $password) {
		$this->__constructor($server,$database,$username,$password);
	}
	
	/**
	 * Constructs a new instance.
	 * 
	 * The constructor constructs a new instance of the class connecting
	 * to the database server with the specified values and selecting the
	 * specified database.  After construction is called hasError() 
	 * should be checked to see if connection was successful.
	 * 
	 * @since 0.01
	 * 
	 * @param string $server The name of the server.
	 * @param string $database The name of the database.
	 * @param string $username The user name.
	 * @param string $password The user password.
	 */
	function __constructor($server, $database, $username, $password) {
		
		if (defined('CPG_DB_DEBUG') and (CPG_DB_DEBUG == true)) {
			$this->show_errors();
		}
		
		$this->m_server   = $server;
		$this->m_database = $database;
		$this->m_username = $username;
		$this->m_password = $password;
		
		$this->m_dbh = mysql_connect($server, $username, $password, true);
		if (!$this->m_dbh) {
			$this->setError("<div class=\"cpg_db_error\"><h1 class=\"cpg_db_error\">Error</h1>".
			                "Cannot connect to database server! - ".mysql_error()."</div>");
			return;
		} else {
			if (!mysql_select_db($database, $this->m_dbh)) {
				$this->setError("<div class=\"cpg_db_error\"><h1 class=\"cpg_db_error\">Error</h1>".
				                "Cannot select server! - ".mysql_error()."</div>");
			} else {
				$this->m_connected = true;
			}
		}
	}
	
	/**
	 * Perform a database query.
	 * 
	 * The method performs a database query on the connected database.
	 * 
	 * @since 0.01
	 * 
	 * @param string $query The query.
	 * @return bool|array The result of the query or false.
	 */
	function query($query) {
		if (!$this->m_connected) {
			return false;
		}
		
		$result = mysql_query($query, $this->m_dbh);
		if (!$result) {
			$this->setError("<div class=\"cpg_db_error\"><h1 class=\"cpg_db_error\">Error</h1>".
				     "Query failed! - ".mysql_error()."</div>");
			return false;
		}
		
		$rows = array();
		
		while ($row = mysql_fetch_assoc($result)) {
			array_push($rows, $row);
		}
		
		mysql_free_result($result);
		
		return $rows;
	}
	
	/**
	 * Enables the display of errors.
	 * 
	 * The method enables the display of errors.  If false is passed as the
	 * parameter value the display of errors is disabled.
	 * 
	 * @since 0.01
	 * 
	 * @param bool $show true to enable display of errors.
	 */
	function showErrors($show = true) {
		$this->m_show_errors = $show;
	}
	
	/**
	 * Sets an error condition.
	 * 
	 * This method sets an error condition with the specified error message.
	 * Once an error condition has been set hasError() will return true and
	 * getError() will return the last error.  The error can be cleared with
	 * clearError().
	 * 
	 * @since 0.01
	 * 
	 * @param string $msg The error message.
	 */
	function setError($msg) {
		$this->m_has_error  = true;
		$this->m_last_error = $msg;
	}
	
	/**
	 * Returns the error condition status.
	 * 
	 * The method returns true if an error has occurred otherwise it returns
	 * false.  getError() will return the error and clearError() will clean
	 * an outstanding error condition.
	 * 
	 * @since 0.01
	 * 
	 * @return bool true if an error has occurred.
	 */
	function hasError() {
		return $this->m_has_error;
	}
	
	/**
	 * Returns the last error.
	 * 
	 * The method returns the last error or null if an error condition is not
	 * outstanding.
	 * 
	 * @since 0.01
	 * 
	 * @return null|string The error or null.
	 */
	function getError() {
		return $this->m_last_error;
	}
	
	/**
	 * Clears an error condition.
	 * 
	 * The method clears an error condition.  After calling this method
	 * hasError() will return false and getError() will return null.
	 * 
	 * @since 0.01
	 */
	function clearError() {
		$this->m_has_error  = false;
		$this->m_last_error = null;
	}
}

?>
