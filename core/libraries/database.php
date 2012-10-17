<?php

if (!defined('CORE_PATH'))
	exit('No direct script access allowed');


class rpd_database_library
{

	public $database;
	public $hostname;
	public $username;
	public $password;
	public $dbdriver;
	public $dbprefix = '';
	public $port = '';
	public $conn_id = FALSE;
	public $result_id = FALSE;
	public $db_debug = FALSE;
	public $result_array = array();
	public $result_object = array();
	public $last_query;
	public $queries = array();

	/**
	 * when rapyd is used as "library" it can use a valid connection resource link
	 * if parent framework already instanced a connection
	 * 
	 * @param object $conn_id 
	 */
	public function __construct($conn_id=FALSE)
	{
		$this->conn_id = $conn_id;
	}

	/**
	 * prepp a value using gettype how to escape
	 * 
	 * @param mixed $str the value to prepp
	 * @return mixed  escaped value 
	 */
	public function escape($str)
	{
		switch (gettype($str))
		{
			case 'string' : $str = "'" . $this->escape_str($str) . "'";
				break;
			case 'boolean' : $str = ($str === FALSE) ? 0 : 1;
				break;
			default : $str = ($str === NULL) ? 'NULL' : $str;
				break;
		}
		return $str;
	}

	/**
	 * execute a query and return result_id object
	 * show error only if db_debug == TRUE
	 * 
	 * @param string $sql
	 * @return object 
	 */
	public function query($sql)
	{
		$this->queries[] = $sql;
		if (FALSE === ($this->result_id = $this->execute($sql)))
		{
			if ($this->db_debug)
			{
				
				return $this->show_error(array($this->error_number($this->conn_id), $this->error_message($this->conn_id), $sql));
			}
			return $this->show_error('DATABASE ERROR');
		}
        
		$this->last_query = $sql;
		return $this->result_id;
	}

	/**
	 * return entire resultset as multi-dimensional associative array
	 * 
	 * @return array the resultset
	 */
	public function result_array($field='')
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_assoc())
		{
			if ($field!='')
			{
				$this->result_array[$row[$field]] = $row;
			} else {
				$this->result_array[] = $row;
			}
		}

		return $this->result_array;
	}

	/**
	 * return current row as associative array
	 * 
	 * @return array current row 
	 */
	public function row_array()
	{
		return $this->fetch_assoc();
	}

	/**
	 * return current row as object
	 * 
	 * @return object 
	 */
	public function row_object()
	{
		return $this->fetch_object();
	}

	/**
	 * same as result_array() but using first result column as index and second as value
	 * used to get data ready for dropdowns, checkboxgroups, or custom "menu" structures
	 * 
	 * @return array 
	 */
	public function options_array()
	{
		if (count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_row())
		{
			switch (count($row))
			{
				case 2:
					$data[$row[0]] = $row[1];
					break;
				case 3:
					$data[$row[0]][$row[1]] = $row[2];
					break;
				default: return array();
			}
		}
		$this->result_array = $data;
		return $this->result_array;
	}

	/**
	 * return entire resultset as multi-dimensional array containing row-objects
	 * 
	 * @return array of objects 
	 */
	public function result_object()
	{
		if (count($this->result_object) > 0)
		{
			return $this->result_object;
		}

		if ($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->data_seek(0);
		while ($row = $this->fetch_object())
		{
			$this->result_object[] = $row;
		}

		return $this->result_object;
	}

	/**
	 * cont all records in a table
	 * 
	 * @todo check if "escape_table" works then remove "if"
	 * @param string $table
	 * @return int  record number
	 */
	public function count_all($table)
	{
		if ($this->dbdriver == 'pgsql')
		{
			$this->query("SELECT COUNT(*) AS numrows FROM " . $this->dbprefix . $table);
		} else
		{
			$this->query("SELECT COUNT(*) AS numrows FROM `" . $this->dbprefix . $table . "`");
		}

		if ($this->num_rows() == 0)
			return '0';

		$row = $this->result_object();
		return $row[0]->numrows;
	}

	/**
	 * show all tables in current db
	 * 
	 * @todo check if it's mysql-specific or standard ansi
	 * @todo check if "escape_table" works with all drivers, then use it instead plain backtrics
	 * @return array tables list
	 */
	public function list_tables()
	{
		$retval = array();
		$this->query("SHOW TABLES FROM `" . $this->database . "`");
		if ($this->num_rows() > 0)
		{
			foreach ($this->result_array() as $row)
			{
				if (isset($row['TABLE_NAME']))
				{
					$retval[] = $row['TABLE_NAME'];
				} else
				{
					$retval[] = array_shift($row);
				}
			}
		}
		return $retval;
	}

	/**
	 * show all fields of given table
	 * 
	 * @todo check if it's mysql-specific or standard ansi
	 * @param string $table
	 * @return array field list of given table 
	 */
	public function list_fields($table = '')
	{
		$retval = array();
		$this->query("SHOW COLUMNS FROM " . self::escape_table($table));
		foreach ($this->result_array() as $row)
		{
			if (isset($row['COLUMN_NAME']))
			{
				$retval[] = $row['COLUMN_NAME'];
			} else
			{
				$retval[] = current($row);
			}
		}
		return $retval;
	}

	/**
	 * show field structure of given table
	 * 
	 * @todo try to move conditional dbdriver-if to correct driver 
	 * @param string $table
	 * @return array 
	 */
	public function field_data($table)
	{
		$retval = array();
		$this->query("SELECT * FROM " . self::escape_table($this->dbprefix . $table) . " LIMIT 1");
		while ($field = $this->fetch_field())
		{
			if ($this->dbdriver == 'pgsql')
			{
				$retval = $this->fetch_field();
			} else
			{
				$retval[] = $field;
			}
		}
		return $retval;
	}

	/**
	 * prepp table name inside queries
	 * 
	 * @todo check if it works for all drivers
	 * @param string $table
	 * @return string 
	 */
	protected static function escape_table($table)
	{
		if (stristr($table, '.'))
		{
			$table = preg_replace("/\./", "`.`", $table);
		}
		return $table;
	}

	/**
	 * internal shortcut to show an application error
	 * 
	 * @todo test&debug
	 * @param string $message 
	 */
	protected function show_error($message)
	{
		rpd::error(500, implode(', ', (!is_array($message)) ? array($message) : $message));
	}

}
