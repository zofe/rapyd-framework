<?php

if (!defined('CORE_PATH'))
	exit('No direct script access allowed');


class rpd_database_ar_library extends rpd_database_library
{

	public $ar_select = array();
	public $ar_distinct = FALSE;
	public $ar_from = array();
	public $ar_join = array();
	public $ar_where = array();
	public $ar_like = array();
	public $ar_groupby = array();
	public $ar_having = array();
	public $ar_limit = FALSE;
	public $ar_offset = FALSE;
	public $ar_order = FALSE;
	public $ar_orderby = array();
	public $ar_set = array();
	public $last_vars = array();

	/**
	 * prepare select statement
	 * and return $this, to ensure method chaining:
	 * $this->db->select('*')->from(...
	 * 
	 * @param string $select fields to select
	 * @return object $this 
	 */
	public function select($select = '*')
	{
		if (is_string($select))
		{
			$select = explode(',', $select);
		}

		foreach ($select as $val)
		{
			$val = trim($val);

			if ($val != '')
				$this->ar_select[] = $val;
		}
		return $this;
	}

	/**
	 * prepare distinct statement
	 * 
	 * @param mixed $val true or fieldname
	 * @return object $this 
	 */
	public function distinct($val = TRUE)
	{
		$this->ar_distinct = (is_bool($val)) ? $val : TRUE;
		return $this;
	}

	/**
	 * prepare from statement
	 * 
	 * @param string $from table name
	 * @return object $this 
	 */
	public function from($from)
	{
		foreach ((array) $from as $val)
		{
			$this->ar_from[] = $this->dbprefix . $val;
		}
		return $this;
	}

	/**
	 * prepare a join
	 * 
	 * @param string $table table name
	 * @param string $cond join condition: 'tb1.rel_field = tb2.field'
	 * @param string $type join direction by default INNER
	 * @return object $this 
	 */
	public function join($table, $cond, $type = '')
	{
		if ($type != '')
		{
			$type = strtoupper(trim($type));

			if (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE))
			{
				$type = '';
			} else
			{
				$type .= ' ';
			}
		}

		if ($this->dbprefix)
		{
			$cond = preg_replace('|([\w\.]+)([\W\s]+)(.+)|', $this->dbprefix . "$1$2" . $this->dbprefix . "$3", $cond);
		}

		// If a DB prefix is used we might need to add it to the column names
		if ($this->dbprefix)
		{
			// First we remove any existing prefixes in the condition to avoid duplicates
			$cond = preg_replace('|(' . $this->dbprefix . ')([\w\.]+)([\W\s]+)|', "$2$3", $cond);

			// Next we add the prefixes to the condition
			$cond = preg_replace('|([\w\.]+)([\W\s]+)(.+)|', $this->dbprefix . "$1$2" . $this->dbprefix . "$3", $cond);
		}

		$this->ar_join[] = $type . 'JOIN ' . $this->dbprefix . $table . ' ON ' . $cond;
		return $this;
	}

	/**
	 * prepare a where clause
	 * using this function it merge using "AND" operator
	 * 
	 * @param string $key field name or an associative array $key=>$field
	 * @param mixed $value field value
	 * @return object $this 
	 */
	public function where($key, $value = NULL)
	{
		return $this->_where($key, $value, 'AND ');
	}

	/**
	 * prepare a where clause
	 * using this function it merge using "OR" operator
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @return object $this 
	 */
	public function orwhere($key, $value = NULL)
	{
		return $this->_where($key, $value, 'OR ');
	}

    
	/**
     * todo fix with escape
	 * append a where clause this way: "  AND ($value OR $orvalue) "
	 * $orvalue must be  a bulk  "key=value"
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @return object $this 
	 */
	public function whereor($key, $value, $orvalue)
	{
        $prefix = (count($this->ar_where) == 0) ? '' : ' AND ';
        $key .= (!$this->_has_operator($key))? ' =' : '';
        $value = $this->escape($value);
        $this->ar_where[] = $prefix.' ('.$key.$value.' OR '.$orvalue.' )';
        return $this;
	}

	/**
	 * internal, called by where/orwhere
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @param string $type
	 * @return object $this
	 */
	protected function _where($key, $value = NULL, $type = 'AND ')
	{
		if (!is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->ar_where) == 0) ? '' : $type;

			if (!is_null($v))
			{
				if (!$this->_has_operator($k))
				{
					$k .= ' =';
				}

				$v = ' ' . $this->escape($v);
			}

			$this->ar_where[] = $prefix . $k . $v;
		}
		return $this;
	}

	/**
	 * prepare a where-like clause comparison
	 * using this function it merge using "AND" operator
	 * 
	 * @param string $field field name
	 * @param string $match it expects you use jolly char %
	 * @return object $this 
	 */
	public function like($field, $match = '')
	{
		return $this->_like($field, $match, 'AND ');
	}

	/**
	 * prepare a where-like clause comparison
	 * using this function it merge using "OR" operator
	 * 
	 * @param string $field field name
	 * @param string $match match string
	 * @return object $this 
	 */
	public function orlike($field, $match = '')
	{
		return $this->_like($field, $match, 'OR ');
	}

	/**
	 * internal, called by like/orlike
	 * 
	 * @param string $field
	 * @param string $match
	 * @param string $type
	 * @return object $this 
	 */
	protected function _like($field, $match = '', $type = 'AND ')
	{
		if (!is_array($field))
		{
			$field = array($field => $match);
		}

		foreach ($field as $k => $v)
		{
			$prefix = (count($this->ar_like) == 0) ? '' : $type;

			$v = $this->escape_str($v);

			$this->ar_like[] = $prefix . " $k LIKE '%{$v}%'";
		}
		return $this;
	}

	/**
	 * prepare a group by clause 
	 * 
	 * @param string $by field name
	 * @return object $this 
	 */
	public function groupby($by)
	{
		if (is_string($by))
		{
			$by = explode(',', $by);
		}

		foreach ($by as $val)
		{
			$val = trim($val);

			if ($val != '')
				$this->ar_groupby[] = $val;
		}
		return $this;
	}

	/**
	 * prepare an having clause 
	 * using this function it merge using "AND" operator
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @return type 
	 */
	public function having($key, $value = '')
	{
		return $this->_having($key, $value, 'AND ');
	}

	/**
	 * prepare an having clause 
	 * using this function it merge using "OR" operator
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @return object $this 
	 */
	public function orhaving($key, $value = '')
	{
		return $this->_having($key, $value, 'OR ');
	}

	/**
	 * internal, called by having/orhaving
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @param string $type
	 * @return object $this 
	 */
	protected function _having($key, $value = '', $type = 'AND ')
	{
		if (!is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$prefix = (count($this->ar_having) == 0) ? '' : $type;

			if ($v != '')
			{
				$v = ' ' . $this->escape($v);
			}

			$this->ar_having[] = $prefix . $k . $v;
		}
		return $this;
	}

	/**
	 * prepare an order by clause 
	 * 
	 * @param string $orderby field name
	 * @param type $direction direction
	 * @return object $this 
	 */
	public function orderby($orderby, $direction = '')
	{
		if (trim($direction) != '')
		{
			$direction = (in_array(strtoupper(trim($direction)), array('ASC', 'DESC', 'RAND()'), TRUE)) ? ' ' . $direction : ' ASC';
		}

		$this->ar_orderby[] = $orderby . $direction;
		return $this;
	}

	/**
	 * prepare a limit/offset clause 
	 * 
	 * @param int $value row number
	 * @param mixed $offset starting from
	 * @return object $this 
	 */
	public function limit($value, $offset = '')
	{
		$this->ar_limit = $value;

		if ($offset != '')
			$this->ar_offset = $offset;

		return $this;
	}

	/**
	 * prepare the offset clause, only if you use limit() first
	 * 
	 * @param int $value
	 * @return object $this 
	 */
	public function offset($value)
	{
		$this->ar_offset = $value;
		return $this;
	}

	/**
	 * prepare a insert or update setting field value
	 * 
	 * @param string $key field name
	 * @param mixed $value field value
	 * @param type $escape if value must be escaped (by default true) set it to false if you need to use "db-functions" like NOW()
	 * @return object $this 
	 */
	public function set($key, $value = '', $escape=true)
	{
		$key = $this->_object_to_array($key);

		if (!is_array($key))
		{
			$key = array($key => $value);
		}

		foreach ($key as $k => $v)
		{
			$v = ($escape) ? $this->escape($v) : $v;
			$this->ar_set[$k] = $v;
		}

		return $this;
	}

	/**
	 * prepare a select-count query,
	 * by default it work like get() but using "select count(*)" instead of "select ..."
	 * 
	 * @return int num rows  
	 */
	public function count()
	{
		$this->_save_vars();

		$sql = 'SELECT COUNT(*) AS rows';

		if (count($this->ar_from) > 0)
		{
			$sql .= "\nFROM ";
			$sql .= implode(', ', $this->ar_from);
		}

		if (count($this->ar_join) > 0)
		{
			$sql .= "\n";
			$sql .= implode("\n", $this->ar_join);
		}

		if (count($this->ar_where) > 0 OR count($this->ar_like) > 0)
		{
			$sql .= "\nWHERE ";
		}

		$sql .= implode("\n", $this->ar_where);

		if (count($this->ar_like) > 0)
		{
			if (count($this->ar_where) > 0)
			{
				$sql .= " AND ";
			}

			$sql .= implode("\n", $this->ar_like);
		}

		if (count($this->ar_groupby) > 0)
		{
			$sql .= "\nGROUP BY ";
			$sql .= implode(', ', $this->ar_groupby);
		}

		if (count($this->ar_having) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $this->ar_having);
		}
		$sql .= "\n";
		$sql = $this->_limit($sql, 1, 0);

		$result = $this->query($sql);

		$this->_reset_select();
		return @$this->row_object()->rows;
	}

	/**
	 * execute a "select" query compiling all filled clauses 
	 * and return result identifier object 
	 * after that it reset all clauses to be ready for another query
	 * 
	 * optionally you can pass this function: $table, $limit, $offset
	 * otherwise it use prefilled "from", "limit", "offset" clauses
	 * 
	 * @param string $table
	 * @param int $limit
	 * @param int $offset
	 * @return object result identifier object 
	 */
	public function get($table = '', $limit = null, $offset = null)
	{
		if ($table != '')
		{
			$this->from($table);
		}

		if (!is_null($limit))
		{
			$this->limit($limit, $offset);
		}

		$sql = $this->_compile_select();

		$result = $this->query($sql);
		$this->_reset_select();
		return $result;
	}

	/**
	 * execute a "select" query compiling all filled clauses 
	 * and return result identifier object 
	 * after that it reset all clauses to be ready for another query
	 * 
	 * optionally you can pass this function: $table, $where, $limit, $offset
	 * otherwise it use prefilled "from", "where", "limit", "offset" clauses
	 * 
	 * @todo it's near the same of get.. optimize code 
	 * @param string $table
	 * @param string $where
	 * @param int $limit
	 * @param int $offset
	 * @return object result identifier object 
	 */
	public function getwhere($table = '', $where = null, $limit = null, $offset = null)
	{
		if ($table != '')
		{
			$this->from($table);
		}

		if (!is_null($where))
		{
			$this->where($where);
		}

		if (!is_null($limit))
		{
			$this->limit($limit, $offset);
		}

		$sql = $this->_compile_select();

		$result = $this->query($sql);
		$this->_reset_select();
		return $result;
	}

	/**
	 * execute an "insert" query compiling into/values clauses 
	 * after that it reset write clauses to be ready for another query
	 * 
	 * @param string $table
	 * @param array $set array fieldname=>value
	 * @return object result identifier object 
	 * 
	 */
	public function insert($table = '', $set = NULL)
	{
		if (!is_null($set))
		{
			$this->set($set);
		}

		if (count($this->ar_set) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_must_use_set');
			}
			return FALSE;
		}

		if ($table == '')
		{
			if (!isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}

			$table = $this->ar_from[0];
		}

		$sql = $this->_insert($this->dbprefix . $table, array_keys($this->ar_set), array_values($this->ar_set));

		$this->_reset_write();
		return $this->query($sql);
	}

	/**
	 * execute an "update" query compiling set/where clauses 
	 * after that it reset write clauses to be ready for another query
	 * 
	 * @param string $table
	 * @param array $set
	 * @param mixed $where
	 * @return object result identifier object 
	 */
	public function update($table = '', $set = NULL, $where = null)
	{
		if (!is_null($set))
		{
			$this->set($set);
		}

		if (count($this->ar_set) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_must_use_set');
			}
			return FALSE;
		}

		if ($table == '')
		{
			if (!isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}
			$table = $this->ar_from[0];
		}

		if ($where != null)
		{
			$this->where($where);
		}

		$sql = $this->_update($this->dbprefix . $table, $this->ar_set, $this->ar_where);

		$this->_reset_write();
		return $this->query($sql);
	}

	/**
	 * execute a "delete" query compiling where clause
	 * after that it reset write clauses to be ready for another query
	 * 
	 * @param string $table
	 * @param mixed $where
	 * @return object result identifier object 
	 */
	public function delete($table = '', $where = '')
	{
		if ($table == '')
		{
			if (!isset($this->ar_from[0]))
			{
				if ($this->db_debug)
				{
					return $this->show_error('db_must_set_table');
				}
				return FALSE;
			}

			$table = $this->ar_from[0];
		}

		if ($where != '')
		{
			$this->where($where);
		}

		if (count($this->ar_where) == 0)
		{
			if ($this->db_debug)
			{
				return $this->show_error('db_del_must_use_where');
			}
			return FALSE;
		}

		$sql = $this->_delete($this->dbprefix . $table, $this->ar_where);

		$this->_reset_write();
		return $this->query($sql);
	}

	/**
	 * internal, called by insert
	 * 
	 * @param string $table
	 * @param array $keys
	 * @param array $values
	 * @return string 
	 */
	protected function _insert($table, $keys, $values)
	{
		return "INSERT INTO ".self::escape_table($table)." (".implode(', ', $this->escape_field($keys)).") VALUES (".implode(', ', $values).")";
	}

	/**
	 * internal, called by update
	 * 
	 * @param string $table
	 * @param array $values
	 * @param array $where
	 * @return string 
	 */
	protected function _update($table, $values, $where)
	{
		foreach ($values as $key => $val)
		{
			$valstr[] = $key . " = " . $val;
		}

		return "UPDATE " . self::escape_table($table) . " SET " . implode(', ', $valstr) . " WHERE " . implode(" ", $where);
	}

	/**
	 * internal, called by delete
	 * 
	 * @param string $table
	 * @param array $where
	 * @return string 
	 */
	protected function _delete($table, $where)
	{
		return "DELETE FROM " . self::escape_table($table) . " WHERE " . implode(" ", $where);
	}

	/**
	 * internal, called by limit
	 * 
	 * @param string $sql
	 * @param int $limit
	 * @param int $offset
	 * @return string 
	 */
	protected function _limit($sql, $limit, $offset)
	{
		if ($offset == 0)
		{
			$offset = '';
		} else
		{
			$offset .= ", ";
		}

		return $sql . "LIMIT " . $offset . $limit;
	}

	/**
	 * internal, called by where
	 * 
	 * @param string $str
	 * @return bool 
	 */
	protected function _has_operator($str)
	{
		$str = trim($str);
		if (!preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * internal, called by get/getwhere
	 * 
	 * @return string 
	 */
	public function _compile_select()
	{
		$this->_save_vars();

		$sql = (!$this->ar_distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

		$sql .= ( count($this->ar_select) == 0) ? '*' : implode(', ', $this->ar_select);

		if (count($this->ar_from) > 0)
		{
			$sql .= "\nFROM ";
			$sql .= implode(', ', $this->ar_from);
		}

		if (count($this->ar_join) > 0)
		{
			$sql .= "\n";
			$sql .= implode("\n", $this->ar_join);
		}

		if (count($this->ar_where) > 0 OR count($this->ar_like) > 0)
		{
			$sql .= "\nWHERE ";
		}

		$sql .= implode("\n", $this->ar_where);

		if (count($this->ar_like) > 0)
		{
			if (count($this->ar_where) > 0)
			{
				$sql .= " AND ";
			}

			$sql .= implode("\n", $this->ar_like);
		}

		if (count($this->ar_groupby) > 0)
		{
			$sql .= "\nGROUP BY ";
			$sql .= implode(', ', $this->ar_groupby);
		}

		if (count($this->ar_having) > 0)
		{
			$sql .= "\nHAVING ";
			$sql .= implode("\n", $this->ar_having);
		}

		if (count($this->ar_orderby) > 0)
		{
			$sql .= "\nORDER BY ";
			$sql .= implode(', ', $this->ar_orderby);

			if ($this->ar_order !== FALSE)
			{
				$sql .= ( $this->ar_order == 'desc') ? ' DESC' : ' ASC';
			}
		}

		if (is_numeric($this->ar_limit))
		{
			$sql .= "\n";
			$sql = $this->_limit($sql, $this->ar_limit, $this->ar_offset);
		}

		return $sql;
	}

	/**
	 * internal, utility
	 * 
	 * @todo check if php5 support a native conversion
	 * @param type $object
	 * @return type 
	 */
	protected function _object_to_array($object)
	{
		if (!is_object($object))
		{
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			if (!is_object($val) AND !is_array($val))
			{
				$array[$key] = $val;
			}
		}

		return $array;
	}

	/**
	 * internal, called after a get/getwhere to reset clauses
	 */
	protected function _reset_select()
	{
		$this->ar_select = array();
		$this->ar_distinct = FALSE;
		$this->ar_from = array();
		$this->ar_join = array();
		$this->ar_where = array();
		$this->ar_like = array();
		$this->ar_groupby = array();
		$this->ar_having = array();
		$this->ar_limit = FALSE;
		$this->ar_offset = FALSE;
		$this->ar_order = FALSE;
		$this->ar_orderby = array();
	}

	/**
	 * utility, used for example after a count();
	 * it restore last stored clause status 
	 * 
	 * @todo it's really needed? or we can just comment _reset_select() inside count() ?
	 */
	public function refill_query()
	{
		foreach ($this->last_vars as $clause => $value)
		{
			if (substr($clause, 0, 3) == 'ar_')
				$this->$clause = $value;
		}
	}

	/**
	 * internal, save all clauses in a property
	 * 
	 * @todo it's really needed? or we can just comment _reset_select() inside count() ?
	 */
	protected function _save_vars()
	{
		$this->last_vars = get_object_vars($this);
	}

	/**
	 * internal, reset some clauses after insert/update/delete
	 */
	protected function _reset_write()
	{
		$this->ar_set = array();
		$this->ar_from = array();
		$this->ar_where = array();
	}

}
