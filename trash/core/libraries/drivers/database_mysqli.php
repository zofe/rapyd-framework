<?php



class rpd_database_mysqli_driver extends rpd_database_ar_library {



	public function connect()
	{
			$this->conn_id = mysqli_connect($this->hostname, $this->username, $this->password);
			return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = mysqli_connect("p:".$this->hostname, $this->username, $this->password);
		return $this->conn_id;
	}

	public function select_db()
	{
		return @mysqli_select_db( $this->conn_id, $this->database);
	}

	public function db_set_charset($charset, $collation)
	{
		return @mysqli_query("SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'", $this->conn_id);
	}

	protected function execute($sql)
	{

		//rrr : reset result resources
		$this->result_id     = FALSE;
		$this->result_array = array();
		$this->result_object = array();
                @mysqli_query($this->conn_id, "SET CHARACTER SET utf8");
                @mysqli_query($this->conn_id, "SET NAMES utf8");
		return @mysqli_query($this->conn_id, $sql);
		if (!$resurce and $this->db_debug)
		{
			'<pre>'.$sql.'</pre>';
		}
		return $resurce;

	}

	protected function escape_str($str)
	{
		if (function_exists('mysqli_real_escape_string'))
		{
			return mysqli_real_escape_string($this->conn_id, $str);
		}
		elseif (function_exists('mysqli_escape_string'))
		{
			return mysqli_escape_string($this->conn_id, $str);
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
		return @mysqli_affected_rows($this->conn_id);
	}

	public function insert_id()
	{
		return @mysqli_insert_id($this->conn_id);
	}

	public function data_seek($n = 0)
	{
		return mysqli_data_seek($this->result_id, $n);
	}

	public function fetch_row()
	{
		return mysqli_fetch_row($this->result_id);
	}

	public function fetch_assoc()
	{
		return mysqli_fetch_assoc($this->result_id);
	}

	public function fetch_object()
	{
		return mysqli_fetch_object($this->result_id);
	}

	public function fetch_field()
	{
		$mysql_data_type_hash = array(
			1=>'tinyint',
			2=>'smallint',
			3=>'int',
			4=>'float',
			5=>'double',
			7=>'timestamp',
			8=>'bigint',
			9=>'mediumint',
			10=>'date',
			11=>'time',
			12=>'datetime',
			13=>'year',
			16=>'bit',
			252=>'text',//is currently mapped to all text and blob types (MySQL 5.0.51a)
			253=>'varchar',
			254=>'char',
			246=>'decimal'
		);

		//$fields = mysqli_fetch_field($this->result_id);
		//var_dump($fields);
		//$fields = mysqli_fetch_field($this->result_id);
		//var_dump($fields);
		//for ($i = 0; $i < $this->num_fields(); $i++)
		//foreach($fields as $meta)
		//{

			$meta = mysqli_fetch_field($this->result_id);
		    if ($meta)
			{
				$meta->type 		= $mysql_data_type_hash[$meta->type];
				$meta->primary_key  = ($meta->flags & 2) ? 1 : 0;
				$meta->default		= '';
			}
			return $meta;
			//$retval[] = $meta;
		//}
		
		//return $retval;
	}

	public function num_rows()
	{
		return @mysqli_num_rows($this->result_id);
	}

	public function error_message()
	{
		return mysqli_error($this->conn_id);
	}

	public function error_number()
	{
		return mysqli_errno($this->conn_id);
	}

}
