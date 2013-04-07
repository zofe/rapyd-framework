<?php



class rpd_database_sqlite_driver extends rpd_database_ar_library {

    public $ar_match = array();

	public function connect()
	{
		$this->conn_id = @sqlite_open($this->database, 0666);
		return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = @sqlite_popen($this->database, 0666);
		return $this->conn_id;
	}

	public function select_db()
	{
		return TRUE;
	}

	public function db_set_charset($charset, $collation)
	{
		return TRUE;
	}

	protected function execute($sql)
	{
		//rrr : reset result resources
		$this->result_id     = FALSE;
		$this->result_array = array();
		$this->result_object = array();

		return @sqlite_query($this->conn_id, $sql);

	}

	protected static function escape_str($str)
	{
		if (function_exists('sqlite_escape_string'))
		{
			return sqlite_escape_string($str);
		}
		else
		{
			return addslashes($str);
		}
	}

	// --------------------------------------------------------------------
	protected static function escape_field($fieldname)
	{
		return $fieldname;
	}

	
	public function affected_rows()
	{
		return sqlite_changes($this->conn_id);
	}

	public function insert_id()
	{
		return @sqlite_last_insert_rowid($this->conn_id);
	}

	public function data_seek($n = 0)
	{
		return sqlite_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return sqlite_fetch_array($this->result_id, SQLITE_NUM);
	}

	public function fetch_assoc()
	{
		return sqlite_fetch_array($this->result_id, SQLITE_ASSOC);
	}

	public function fetch_object()
	{
		return sqlite_fetch_object($this->result_id);
	}

	public function fetch_field()
	{
		return mysqli_fetch_field($this->result_id);
	}

	public function num_rows()
	{
		return @sqlite_num_rows($this->result_id);
	}

	public function error_message()
	{
		return sqlite_error_string(sqlite_last_error($this->conn_id));
	}

	public function error_number()
	{
		return sqlite_last_error($this->conn_id);
	}

	//basic support for "match" on FTS tables in sqlite
	public function match($field, $match = '', $type='AND ')
	{
		if (!is_array($field))
		{
			$field = array($field => $match);
		}

		foreach ($field as $k => $v)
		{
			$prefix = (count($this->ar_match) == 0) ? '' : $type;

			$v = $this->escape_str($v);

			$this->ar_match[] = $prefix . " $k MATCH '{$v}'";
		}
		return $this;
	}

}
