<?php



class rpd_database_mysql_driver extends rpd_database_ar_library {


	public function connect()
	{
		$this->conn_id = @mysql_connect($this->hostname, $this->username, $this->password, TRUE);
		return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = @mysql_pconnect($this->hostname, $this->username, $this->password);
		return $this->conn_id;
	}

	public function select_db()
	{
		return @mysql_select_db($this->database, $this->conn_id);
	}

	public function db_set_charset($charset, $collation)
	{
		return @mysql_query("SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'", $this->conn_id);
	}

	protected function execute($sql)
	{
		//rrr : reset result resources
		$this->result_id	 = FALSE;
		$this->result_array = array();
		$this->result_object = array();
                @mysql_query("SET CHARACTER SET utf8", $this->conn_id);
                @mysql_query("SET NAMES utf8", $this->conn_id);
		return @mysql_query($sql, $this->conn_id);
		if (!$resurce and $this->db_debug)
		{
			'<pre>'.$sql.'</pre>';
		}
		return $resurce;
	}

	protected static function escape_str($str)
	{
		if (function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($str);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			return mysql_escape_string($str);
		}
		else
		{
			return addslashes($str);
		}
	}

	// --------------------------------------------------------------------
	protected static function escape_field($fieldname)
	{
		if (is_array($fieldname))
		{
			$escaped = array();
			foreach($fieldname as $fld)
			{
				$escaped[] = '`'.$fld.'`';
			}
			return $escaped;
		}
		$fieldname = '`'.$fieldname.'`';
		return $fieldname;
	}

	
	public function affected_rows()
	{
		return @mysql_affected_rows($this->conn_id);
	}

	public function insert_id()
	{
		return @mysql_insert_id($this->conn_id);
	}

	public function data_seek($n = 0)
	{
		return mysql_data_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return mysql_fetch_row($this->result_id);
	}

	public function fetch_assoc()
	{
		return mysql_fetch_assoc($this->result_id);
	}

	public function fetch_object()
	{
		return mysql_fetch_object($this->result_id);
	}

	public function fetch_field()
	{
		return mysql_fetch_field($this->result_id);
	}

	public function num_rows()
	{
		return @mysql_num_rows($this->result_id);
	}

	public function error_message()
	{
		return mysql_error($this->conn_id);
	}

	public function error_number()
	{
		return mysql_errno($this->conn_id);
	}


}
