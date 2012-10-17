<?php



class rpd_database_pdo_driver extends rpd_database_ar_library {

    public $pdo_results = '';
    public $pdo_index = 0;
    public $field_data = array();
    public $ar_match = array();

	public function connect()
	{

		$this->conn_id = new PDO ($this->database, $this->username, $this->password, array(PDO::ATTR_PERSISTENT => false));
		return $this->conn_id;
	}

	public function pconnect()
	{
		$this->conn_id = new PDO ($this->database, $this->username, $this->password, array(PDO::ATTR_PERSISTENT => true));
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
		$this->pdo_results  = '';
		$this->pdo_index = 0;
		$this->result_id     = FALSE;
		$this->result_array = array();
		$this->result_object = array();

		return $this->conn_id->query($sql);

	}

	public function escape_str($str)
	{
		//trick.. database class will add single quotes in escape() method
		//so we trim those added by PDO::quote()
		return trim($this->conn_id->quote($str),"'");

	}

	// --------------------------------------------------------------------
	protected static function escape_field($fieldname)
	{
		return $fieldname;
	}

	
	public function affected_rows()
	{
		return false;//sqlite_changes($this->conn_id);
	}

	public function insert_id()
	{
		return @$this->conn_id->lastInsertId();
	}

	public function data_seek($n = 0)
	{
		$this->pdo_index = $n;
		return TRUE;
	}

	public function fetch_row()
	{
        if (is_array($this->pdo_results))
        {
            $i = $this->pdo_index;
            $this->pdo_index++;

            if (isset($this->pdo_results[$i]))
            {
                $back = array();

                foreach ($this->pdo_results[$i] as $key => $val)
                {
                    $back[] = $val;
                }

                return $back;
            }

            return null;
        }

        return $this->result_id->fetch(PDO::FETCH_NUM);
	}

	public function fetch_assoc()
	{
        if (is_array($this->pdo_results))
        {
            $i = $this->pdo_index;
            $this->pdo_index++;

            if (isset($this->pdo_results[$i]))
            {
                return $this->pdo_results[$i];
            }

			 return null;
        }
        return $this->result_id->fetch(PDO::FETCH_ASSOC);
	}

	public function fetch_object()
	{
        if (is_array($this->pdo_results))
        {
            $i = $this->pdo_index;
            $this->pdo_index++;

            if (isset($this->pdo_results[$i]))
            {
                $back = '';

                foreach ($this->pdo_results[$i] as $key => $val)
                {
                    $back->$key = $val;
                }

                return $back;
            }

            return null;
        }

        return $this->result_id->fetch(PDO::FETCH_OBJ);
	}


	public function field_data($table)
	{
		$retval = array();
		if (!isset($this->field_data[$table]))
		{
			$this->result_id = $this->execute("PRAGMA table_info('".$this->dbprefix.$table."')");
			$this->pdo_results = $this->result_id->fetchAll(PDO::FETCH_ASSOC);
			$this->field_data[$table] =  $this->fetch_field();
			$retval = $this->field_data[$table];
		} else {
			$retval = $this->field_data[$table];
		}

		return $retval;
	}

	public function fetch_field()
	{
        $retval = array();
        $table_info = $this->pdo_results;

        for ($i = 0; $i < count($table_info); $i++)
        {
            $F               = new stdClass();
            $F->name         = $table_info[$i]['name'];
            $F->type         = $table_info[$i]['type'];
            $F->maxlength    = 0;
            $F->primary_key = (bool)$table_info[$i]['pk'];
            $F->default      = $table_info[$i]['dflt_value'];

            $retval[] = $F;
        }
        return $retval;
	}

	public function num_rows()
	{
            if (!$this->pdo_results)
            {
                $this->pdo_results = $this->result_id->fetchAll(PDO::FETCH_ASSOC);
            }

        return sizeof($this->pdo_results);
	}

	public function error_message()
	{
        $infos = $this->conn_id->errorInfo();
        return $infos[2];
	}

	public function error_number()
	{
        $infos = $this->conn_id->errorInfo();
        return $infos[1];
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
