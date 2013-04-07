<?php



class rpd_database_pgsql_driver extends rpd_database_ar_library {

	private function connect_string()
	{
		$components = array(
								'hostname'	=> 'host',
								'port'		=> 'port',
								'username'	=> 'user',
								'password'	=> 'password',
								'database'	=> 'dbname'
							);
		
		$connect_string = "";
		foreach ($components as $key => $val)
		{
			if (isset($this->$key) && $this->$key != '')
			{
				$connect_string .= " $val=".$this->$key;
			}
		}
		return trim($connect_string);
	}
	
	public function connect()
	{
		$this->conn_id = @pg_connect($this->connect_string());
		return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = @pg_pconnect($this->connect_string());
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
		$this->result_id	 = FALSE;
		$this->result_array = array();
		$this->result_object = array();

		return @pg_query($this->conn_id, $sql);

		if (!$resurce and $this->db_debug)
		{
			'<pre>'.$sql.'</pre>';
		}
		return $resurce;

	}

	protected static function escape_str($str)
	{
		if (function_exists('pg_escape_string'))
		{
			return pg_escape_string($str);
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
		return @pg_affected_rows($this->result_id);
	}

	public function insert_id()
	{
		$v = $this->_version();
		$v = $v['server'];

		$table	= func_num_args() > 0 ? func_get_arg(0) : null;
		$column	= func_num_args() > 1 ? func_get_arg(1) : null;
		
		if ($v >= '8.1')
		{
			$sql='SELECT LASTVAL() as ins_id';
		}
		elseif ($table != null && $column != null && $v >= '8.0')
		{
			$sql = sprintf("SELECT pg_get_serial_sequence('%s','%s') as seq", $table, $column);
			$query = $this->query($sql);
			$row = $query->row();
			$sql = sprintf("SELECT CURRVAL('%s') as ins_id", $row->seq);
		}
		elseif ($table != null)
		{
			// seq_name passed in table parameter
			$sql = sprintf("SELECT CURRVAL('%s') as ins_id", $table);
		}
		else
		{
			return null;
		}
		$query = $this->query($sql);
		$ins_id = pg_fetch_result($query, 0, 0);
		return $ins_id;
	}

	public function data_seek($n = 0)
	{
		return pg_result_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return pg_fetch_row($this->result_id);
	}

	public function fetch_assoc()
	{
		return pg_fetch_assoc($this->result_id);
	}

	public function fetch_object()
	{
		return pg_fetch_object($this->result_id);
	}

	public function fetch_field()
	{
		$retval = array();
		$pkey_arr = $this->fetch_pkey(pg_field_table($this->result_id, 0));
		
		for ($i = 0; $i < $this->num_fields(); $i++)
		{
			$obj = (object) null;
			$obj->name 		= pg_field_name($this->result_id, $i);
			$obj->type 		= pg_field_type($this->result_id, $i);
			$obj->max_length	= pg_field_size($this->result_id, $i);
			$obj->primary_key = 0;			
			foreach ($pkey_arr as $pkey)
			{
				if ($pkey == $obj->name)
				{
					$obj->primary_key = 1;
				}
			}
			$obj->default		= '';
			
			$retval[] = $obj;
		}
		return $retval;
	}
	
	private function fetch_pkey($tablename)
	{
		$tablename = pg_escape_string($tablename);
		$res = pg_query($this->conn_id, "select conkey from pg_constraint join pg_class on pg_class.oid=conrelid where contype='p' and relname = '$tablename';");
		
		if (pg_num_rows($res) == 0)
    {
			$val = '{1}';
		} 
    else 
    {
			$val = pg_fetch_result($res, 0, 0);
		}
		
		$fields = explode(' ', preg_replace('/\{(.*)\}/', '$1', $val));
		$pkey = array();
		foreach ($fields as $field)
		{
			$res = pg_query($this->conn_id, "select attname from pg_class, pg_attribute where relname = '$tablename' and pg_class.oid= attrelid and attnum = $field;");
			$pkey[] = pg_fetch_result($res, 0, 0);
		}
		
		return $pkey;
	}

	private function _version()
	{
		return @pg_version($this->conn_id);
	}

	public function num_fields()
	{
		return @pg_num_fields($this->result_id);
	}

	public function num_rows()
	{
		return @pg_num_rows($this->result_id);
	}

	public function error_message()
	{
#		return pg_last_error($this->conn_id);
		return pg_last_error();
	}

	public function error_number()
	{
		return '';
	}

}
