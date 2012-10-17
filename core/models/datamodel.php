<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


/**
 * MY_Model Class
 *
 * Extend CI_Model to give basic active_record / orm / query bilder  interface  to models
 * This class must be extended and used as..
 * <code>
 * class articles extends datamodel_model {
 *
 *	function __construct() {
 *		parent::__construct();
 *		$this->set_table('tb_articoli');
 *		$this->has_and_belongs_to_many('authors', 'tb_users', 'tb_articles_has_autors', 'ref_id_article', 'ref_id_author'); 
 *	}
 * 
 * class Somecontroller extends Controller {
 *
 *  public function some_method()
 *  {
 *		$articles = new articles_model;

 *		 //create new article 
 *		$articles
 *			->set('title',"New article!")
 *			->set('pub_date','2012-08-15')
 *			->save();
 * 
 *		//get article row array (with primary key = 1), with nested node "authors"  (all fields of both tables)
 *		$article_arr = $articles->load(1)->join('authors')->get();
 * 
 *      //get article row (with primary key = 2), but before, increment views field (views = views+1)
 *		$another_article_arr = $articles->load(2)->inc('views')->get();
 * 
 *		//get last 10 titles of articles for a given author (with primary key = 7),  
 *      $last_articles_titles  = $articles->select('articles.title')
 *                                      ->join('authors')
 *                                      ->where('authors.ID_USER', 7)
 *                                      ->limit(10)
 *                                      ->order_by('articles.pub_data', 'desc')->get();
 * 
 * 
 *		//get all articles with authors, where authors role_id is 5,
 *		//then return a grouped (get_grouped()) result as a muldidimensional array with all node nested and filled 
 *		//note: this perform a "single query" with all needed "deep joins" instancing all needed models to obtain the result
 *     
 *       $articles_of_board = $articles->select('articles.title, 
 *													  articles.pub_data, 
 *													  autors.lastname')
 *											   ->join('authors/roles')
 *											   ->where('roles.role_id',5)
 *											   ->get_grouped();
 *		.....
 * 
 *  }
 * }
 * </code>
 * 
 *  managed rel
 *
 *  x $has_one
 *  x $has_many
 *  x $has_and_belongs_to_many
 *
 *  unmanaged rel
 *
 *  $belongs_to

 * 
 * @author		Felice Ostuni
 * @link		rapyd.com
 */
class rpd_datamodel_model { 

	public $table = null;
	public $loaded = false;
	public $pk = array();
	public $fields = array();
	public $field_meta = array();
	public $data = array();
	public $data_rel = array();
	public $new_data = array();
	public $preprocess_callback = array();
	public $postprocess_callback = array();
	public $postprocess_result = null;
	public $preprocess_result = null;
	public $error_string = ''; //if filled it inject a custom error on validation using this model
	protected $tables_rel = array();
	protected $has_one = array();
	protected $belongs_to = array();
	protected $one_to_one = array();
	protected $one_to_many = array();
	protected $many_to_many = array();
	protected $entities_grouped = array();
	protected $result_entity_pk = array();
	public $fields_grouped = array();
	public $field_names = array();
	static $field_data = array();

	

	public function __construct($table=null) {
        
        rpd::connect();
        $this->db = rpd::$db;
        
		if (isset($table))
            $this->table = $table;
		$this->fields = $this->field_data($this->table);
		$this->entity = strtolower(get_class($this));
		$this->tables_rel = array($this->entity => $this->table);

		// to support tables with one or more PK
		foreach ($this->fields as $field) {
			$this->field_names[] = $field->name;
			$this->field_meta[$field->name] = $field;
			if ($field->primary_key) {
				$this->pk[$field->name] = "";
			}
		}

		if (count($this->pk) == 0) {
			//table must have a PK
			$this->show_error("no PK for " . $table);
			die();
		}
	}

	/**
	 * using "describe"  generates an array of objects containing field meta-data table_name given
	 * 
	 * @param string $table
	 * @return array fields objects array
	 */
	protected function field_data($table) {
		
			if (!isset(self::$field_data[$table])) {
				self::$field_data[$table] = $this->db->field_data($table);
			}
			return self::$field_data[$table];
	}

	/**
	 * return pk name for given table
	 * @param type $table
	 * @return mixed name of pk or false 
	 */
	public function get_pk_name($table) {
		$fields = $this->field_data($table);

		foreach ($fields as $field) {
			if ($field->primary_key) {
				return $field->name;
			}
		}
		return false;
	}



	/**
	 * schedule a callback on $funcion "before" that $action is reached, passing $arr_values.
	 * 
	 * @param mixed $actions array or name of action (insert, update or delete)
	 * @param callback $function
	 * @param array $arr_values
	 */
	public function pre_process($actions, $function, $arr_values = array()) {
		$actions = (array) $actions;
		$object = '';
		foreach ($actions as $action) {
			if (is_array($function) and count($function) == 2) {
				$object = $function[0];
				$function = $function[1];
			}
			$this->preprocess_callback[$action] = array("name" => $function, "arr_values" => $arr_values, "object" => $object);
		}
	}


	/**
 	 * schedule a callback on $funcion "after" that $action is reached, passing $arr_values.
	 * @param mixed $actions array or name of action (insert, update or delete)
	 * @param callback $function
	 * @param array $arr_values
	 */
	public function post_process($actions, $function, $arr_values = array()) {
		$actions = (array) $actions;
		$object = '';
		foreach ($actions as $action) {
			if (is_array($function) and count($function) == 2) {
				$object = $function[0];
				$function = $function[1];
			}
			$this->preprocess_callback[$action] = array("name" => $function, "arr_values" => $arr_values, "object" => $object);
		}
	}

	/**
	 * perform a select on model->pk = $id , result data is stored  and ready for  get(), get(fieldname), etc..
	 * return $this, so you can use method chaining  
	 * 
	 * @param midex $id
	 * @return boolean
	 */
	public function load($id) {
		$this->preprocess_result = null;
		if (is_array($id)) {
			if (sizeof($id) != sizeof($this->pk)) {
				$this->show_error("not enough parameters");
				return false;
			} else {
				foreach ($this->pk as $keyfield => $keyvalue) {
					$this->pk[$keyfield] = $id[$keyfield];
				}
			}
		} else {

			$keys = array_keys($this->pk);
			$key = $keys[0];
			$this->pk[$key] = $id;
		}
		$this->db->getwhere($this->table, $this->pk);

		if ($this->db->num_rows() > 1) {
			$this->show_error("more than one result");
			return false;
		} elseif ($this->db->num_rows() == 1) {

			$results = $this->db->result_array();
			$this->bind_data($results[0]);
			$this->loaded = true;
		} else {
			$this->loaded = false;
		}

		return $this;
	}

	/**
	 * return if record is already loaded, so if model is ready to get(), set(), save()
	 * @return boolean
	 */
	public function loaded() {
		return $this->loaded;
	}

	/**
	 * unload data from the model
	 * it's called before select(), load() etc..
	 */
	public function unload() {
		$this->loaded = false;
		$this->data = array();
		$this->data_rel = array();
		foreach ($this->fields as $field) {
			if ($field->primary_key) {
				$this->pk[$field->name] = "";
			}
		}
	}

	/**
	 * perform an insert or upload, depending of status of "model->loaded"
	 * @return boolean
	 */
	public function save() {
		//INSERT
		if (!$this->loaded) {
			$pk_ai = true;
			foreach ($this->pk as $keyfield => $keyvalue) {
				if (isset($this->data[$keyfield])) {
					$this->pk[$keyfield] = $this->data[$keyfield];
					$pk_ai = false;
				}
			}
			$escape = $this->exec_preprocess_callback("insert");

			if ($escape !== false) {
				$result = $this->db->insert($this->table, $this->data);
				if ($result && $pk_ai) {
					$keys = array_keys($this->pk);
					$key = $keys[0];
					$this->pk[$key] = $this->insert_id();
					$this->data[$key] = $this->pk[$key];
					$this->loaded = true;
					//$this->bind_rel();
				}
				//exec post process function and store result in a property
				$this->postprocess_result = $this->exec_postprocess_callback("insert");
				return $result;
			} else {
				return false;
			}

			//UPDATE
		} else {

			$this->db->where($this->pk);
			$escape = $this->exec_preprocess_callback("update");

			foreach ($this->pk as $keyfield => $keyvalue) {
				if (isset($this->data[$keyfield])) {
					$this->pk[$keyfield] = $this->data[$keyfield];
				}
			}

			if ($escape !== false) {
				if (count($this->new_data) > 0) {
					$result = $this->db->update($this->table, $this->new_data);
				} else {
					$result = true;
				}
				//exec post process function and store result in a property
				$this->postprocess_result = $this->exec_postprocess_callback("update");
				return $result;
			} else {
				return false;
			}
		}
	}

	/**
	 * return last insert id
	 * its used  after an insert to  fill pk value
	 * @return type
	 */
	public function insert_id() {
		return $this->db->insert_id();
	}


	/**
	 * alternative to load(), if you need to specify different key/val
	 * todo: add syntax to get an array (k1=>v1,k2=>v2,..) instead single field=value 
	 * 
	 * @param string $field
	 * @param string $value
	 * @return boolean
	 */
	public function load_where($field, $value) {
		$this->db->where($field, $value);
		$this->db->get($this->table);

		if ($this->db->num_rows() > 1) {
			$this->show_error("more than one result");
			return false;
		} elseif ($this->db->num_rows() === 1) {
			$results = $this->db->result_array();
			$this->bind_data($results[0]);
			foreach ($this->pk as $keyfield => $keyvalue) {
				$this->pk[$keyfield] = $results[0][$keyfield];
			}
			$this->loaded = true;
			//$this->bind_rel();
			return true;
		} else {
			return false;
		}
	}


	/**
	 * check if on model->table  the $value is already taken on $field column
	 * this can be used in form-rules  to prevent record "unique/pk" violation 
	 * 
	 * @param string $field
	 * @param value $value
	 * @return boolean
	 */
	public function is_unique($field, $value) {
		$this->db->where($field, $value);
		$this->db->get($this->table);

		if ($this->db->num_rows() > 1) {
			return false;
		} elseif ($this->db->num_rows() === 1) {
			if ($this->loaded) {
				return ($this->data[$field] == $value);
			} else {
				return false;
			}
		} else {
			return true;
		}
	}


	/**
	 * the same behavior of is_unique(), but using an array of field=>value
	 * this can be used in form-rules  to prevent record "unique/pk" violation 
	 * 
	 * @param type $field
	 * @return boolean
	 */
	public function are_unique($field) {
		if (is_array($field) && count($field) > 0) {
			foreach ($field as $fieldname => $value) {
				$this->db->where($fieldname, $value);
			}
		} else {
			return false;
		}
		$this->db->get($this->table);

		if ($this->db->num_rows() > 1) {
			return false;
		} elseif ($this->db->num_rows() === 1) {
			if ($this->loaded) {
				foreach ($field as $fieldname => $value) {
					if ($this->data[$fieldname] != $value)
						return false;
				}
				return true;
			}
			return false;
		} else {
			return true;
		}
	}

	/**
	 * return field value, if model has a record loaded, or execute the query and return resuldset (if we are here after a select())
	 * 
	 * @param type $field
	 * @return null
	 */
	public function get($field = null) {

		if ($this->loaded) {

			if (!isset($field)) {
				$data = $this->data;
				$data = array_merge($data, $this->data_rel);
				return $data;
			} else {
				if (isset($this->data[$field])) {
					return $this->data[$field];
				} else {
					return null;
				}
			}
		} else {

			$this->db->get();
			if ($this->db->num_rows() > 0) {
				return $this->db->result_array();
			} else {
				return array();
			}
		}
	}

	/**
	 * similar to get(), it return  value of a related join table, (
	 * 
	 * @param string $rel_id
	 * @param string $field
	 * @return mixed 
	 */
	public function get_rel($rel_id, $field) {
		if (isset($this->data_rel[$rel_id][$field])) {
			return $this->data_rel[$rel_id][$field];
		} else {
			return null;
		}
	}

	/**
	 * similar to get_rel(), it return entire resuldset of joined table
	 * 
	 * @param string $rel_id
	 * @return mixed (array or null) 
	 */
	public function get_related($rel_id) {
		if (isset($this->data_rel[$rel_id])) {
			return $this->data_rel[$rel_id];
		} else {
			return null;
		}
	}

	/**
	 * set a $value on $field on current model->table, before a save, it fill  the insert or the update clause
	 * 
	 * @param type $field
	 * @param mixed $value
	 * @return \MY_Model
	 */
	public function set($field, $value) {
		$field_meta = $this->field_meta[$field];
		if (in_array($field_meta->type, array("int", "date")) && $value == "") {
			$value = null;
		}

		//store only new values in a new array
		if (isset($this->data[$field])) {
			if ($value != $this->data[$field])
				$this->new_data[$field] = $value;
		} else {
			if (in_array($field, $this->field_names)) {
				if ($this->loaded && is_null($value)) {
					//is already null
				} else {
					$this->new_data[$field] = $value;
				}
			}
		}
		$this->data[$field] = $value;
		
		return $this;
	}

	/**
	 * set a $value on $field on a related table,  before a save, it fill  the insert or the update clause
	 * 
	 * @param string $rel_id
	 * @param field $field
	 * @param mixed $value
	 * @return \MY_Model
	 */
	public function set_rel($rel_id, $field, $value) {
		$this->data_rel[$rel_id][$field] = $value;
		return $this;
	}

	/**
	 * inc $field value  of 1 or more,  before a save
	 * 
	 * @param string $field
	 * @param int $inc
	 * @return \MY_Model
	 */
	public function inc($field, $inc = 1) {
		if (isset($this->data[$field])) {
			$this->data[$field] = $this->data[$field] + $inc;
		} else {
			$this->data[$field] = $inc;
		}
		return $this;
	}

	/**
	 * dec $field value of 1 or more, keeping value >= 0 or not,  before a save
	 * 
	 * @param string $field
	 * @param int $inc
	 * @return \MY_Model
	 */
	public function dec($field, $dec = 1, $positive = true) {
		if (isset($this->data[$field])) {
			if (($this->data[$field] - $dec < 0) && ($positive)) {
				return false;
			} else {
				$this->data[$field] = $this->data[$field] - $dec;
			}
		} else {
			if ($positive) {
				return false;
			} else {
				$this->data[$field] = 0 - $dec;
			}
		}
		return $this;
	}

	
	/**
	 * delete current loaded record, exec db->delete
	 * 
	 */
	public function delete() {
		if ($this->loaded) {
			$this->db->where($this->pk);

			$escape = $this->exec_preprocess_callback("delete");
			if ($escape !== false) {
				$result = $this->db->delete($this->table);
				$this->postprocess_result = $this->exec_postprocess_callback("delete");
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	/**
	 * delete perform a delete on model->table using $field=>$value as where
	 * 
	 */
	public function delete_where($field, $value) {
		$this->db->where($field, $value);
		return $this->db->delete($this->table);
	}

	/**
	 * declare an has_one relationships with another table to perform correct joins
	 * 
	 * @param string $id	the name of the other "model" 
	 * @param string $table	the name of other model "table"
	 * @param string $field	the name of "field" on the other model table to consider in the "on"
	 * @param string $field_fk	the name of "field" on current model->table  to  consider in the "on"
	 */
	public function has_one($id, $table, $field = "", $field_fk = "{pk}") {
		if ($field == "")
			$field = $field_fk;
		$arr["id"] = $id;
		$arr["table"] = $table;  //table to join
		$this->tables_rel[$id] = $table;
		if (strpos($table, " as") > 0) {
			$alias = substr($table, strpos($table, " as ") + 4);
		} else {
			$alias = $table;
		}
		$arr["table_alias"] = $alias;
		$arr["on"] = $alias . "." . $field . " = " . $this->table . "." . $field_fk;
		$this->one_to_one[$id] = $arr;
	}
    
	/**
	 * declare an belongs_to relationships with another table to perform correct joins
	 * 
	 * @param string $id	the name of the other "model" 
	 * @param string $table	the name of other model "table"
	 * @param string $field	the name of "field" on the other model table to consider in the "on"
	 * @param string $field_fk	the name of "field" on current model->table  to  consider in the "on"
	 */
	public function belongs_to($id, $table, $field = "{table.pk}", $field_fk = "{table.pk}") {
		if ($field == "")
			$field = $field_fk;
		$arr["id"] = $id;
		$arr["table"] = $table;  //table to join
		$this->tables_rel[$id] = $table;
		if (strpos($table, " as") > 0) {
			$alias = substr($table, strpos($table, " as ") + 4);
		} else {
			$alias = $table;
		}
		$arr["table_alias"] = $alias;
		$arr["on"] = $alias . "." . $field . " = " . $this->table . "." . $field_fk;
		$this->one_to_one[$id] = $arr;
	}
    

	/**
	 * declare an has_many relationships with another table to perform correct joins
	 * 
	 * @param string $id	the name of the other "model" 
	 * @param string $table	the name of other model "table"
	 * @param string $field	the name of "field" on the other model table to consider in the "on"
	 * @param string $field_fk	the name of "field" on current model->table  to  consider in the "on"
	 */
	public function has_many($id, $table, $field = "", $field_fk = "{pk}") {
		if ($field == "")
			$field = $field_fk;
		$arr["id"] = $id;
		$arr["table"] = $table;
		$this->tables_rel[$id] = $table;
		if (strpos($table, " as") > 0) {
			$alias = substr($table, strpos($table, " as ") + 4);
		} else {
			$alias = $table;
		}
		$arr["table_alias"] = $alias;
		$arr["on"] = $alias . "." . $field . " = " . $this->table . "." . $field_fk;
		$this->one_to_many[$id] = $arr;
	}

	/**
	 * declare an has_and_belongs_to_many (many_to_many) relationships with another table to perform correct joins eith 2 tables
	 * 
	 * @param string $id		the name of the other "model" 
	 * @param string $table	the name of other model "table"
	 * @param string $rel_table	the name of "table" where n-m rel is stored
	 * @param string $field		the name of "field" on rel_table to consider in the join "on" with current model->table
	 * @param string $rel_field	the name of "field" on rel_table to consider in the join "on" with other table
	 * @param string $field_fk	by default autofilled with model->pk
	 * @param string $table_field_fk	by default autofilled with othermodel->pk
	 */
	public function has_and_belongs_to_many($id, $table, $rel_table, $field = "{pk}", $rel_field = "{table.pk}", $field_fk = "{pk}", $table_field_fk = "{table.pk}") {
		if ($field == "")
			$field = $field_fk;
		$arr["id"] = $id;
		$arr["rel_table"] = $rel_table;
		$arr["table"] = $table;
		$this->tables_rel[$id] = $table;
		if (strpos($table, " as") > 0) {
			$alias = substr($table, strpos($table, " as ") + 4);
		} else {
			$alias = $table;
		}
		$arr["table_alias"] = $alias;
		$arr["on"] = $rel_table . "." . $field . " = " . $this->table . "." . $field_fk;
		$arr["on2"] = $rel_table . "." . $rel_field . " = " . $alias . "." . $table_field_fk;

		$this->many_to_many[$id] = $arr;
	}


	/**
	 * prepare a SELECT statement, use this kind of arguments ('fieldname,fieldname,...') or ('fieldname','fieldname',...) or ('model.fieldname, othermodel.fieldname,...')
	 * IMPORTANT: if you plan to perform join() and get_grouped() using declared relationships you must use last syntax: select('modelname.fieldname, othermodel.fieldname,...')
	 * 
	 * @param string ('modelname.fieldname, othermodel.fieldname,...')
	 * @return \MY_Model
	 */
	public function select() {
		$this->unload();
		$args = func_get_args();

		if (func_num_args() === 1 && strpos($args[0], ',')) {
			$args = explode(',', $args[0]);
		}

		foreach ($args as $arg) {
			$arg = trim($arg);
			if ($arg === "*" || !strpos($arg, '.')) {
				//fare il replace di * con tutti i campi della tabella
				$fields[] = $this->table . "." . $arg;
			} else {

				foreach ($this->tables_rel as $id => $table) {
					$arg = str_replace($id . '.', $table . '.', $arg);
				}
				$fields[] = $arg;
			}
		}
		$fields[] = $this->table . '.' . key($this->pk) . ' as _' . $this->entity . '_pk';
		//$fields =array_unique($fields);

		$this->db->select(implode(",", $fields));
		return $this;
	}


	/**
	 * prepare a JOIN clause, if you need to join "n" models at the same "level" use this kind of arguments ('modelname','othermodelname',...).
	 * if you need  to join some model  not directly related  with current module,  you can use jeep join syntax ('modelname/othermodel/andothermodel') in this case  you can join  in deep  from  your "model" to "andothermodel"
	 * don't worry abount "on",  it will be performed by this class.
	 * IMPORTANT: if you plan to perform join() and get_grouped() using declared relationships you must use last syntax: select('modelname.fieldname, othermodel.fieldname,...')
	 * 
	 * @param mixed ('modelname/othermodel/andothermodel', 'modelname', 'othermodel',...)  see some sample 
	 * @return \MY_Model
	 */
	public function join() {
		$args = func_get_args();

		$deep_joins = array();
		$deep_join = array_search('deep', $args);
		$inner_join = array_search('inner', $args);
		$left_join = (array_search('left', $args));

		if (!$this->loaded) {

			if (!$deep_join) {
				$this->db->from($this->table);
			} else {
				unset($args[$deep_join]);
				$args = array_values($args);
			}

			if ($left_join) unset($args[$left_join]);
			if ($inner_join) 
			{
				$join_type = 'inner';
				unset($args[$inner_join]);
				$args = array_values($args);
			} else {
				$join_type = 'left';
			}


			$pk_name = key($this->pk);



			foreach ($args as $arg) {
				if (strpos($arg, "/")) {
					$deep_joins = explode("/", strtolower($arg));
					$this->entities_grouped[] = $deep_joins;
					$arg = array_shift($deep_joins);
				} else {
					$this->entities_grouped[] = (array)strtolower($arg);
				}

				$one_to_one = array();
				$one_to_many = array();
				$many_to_many = array();

				if (array_key_exists($arg, $this->one_to_one)) {
					$one_to_one = $this->one_to_one[$arg];
					$one_to_one["on"] = str_replace("{pk}", $pk_name, $one_to_one["on"]);
					$one_to_one["on"] = str_replace("{table.pk}", $this->get_pk_name($one_to_one['table']), $one_to_one["on"]);
					$this->db->join($one_to_one["table"], $one_to_one["on"], $join_type);

				} elseif (array_key_exists($arg, $this->one_to_many)) {

					$one_to_many = $this->one_to_many[$arg];
					$one_to_many["on"] = str_replace("{pk}", $pk_name, $one_to_many["on"]);
					$this->db->join($one_to_many["table"], $one_to_many["on"], $join_type);
				} elseif (array_key_exists($arg, $this->many_to_many)) {

					$many_to_many = $this->many_to_many[$arg];
					$many_to_many["on"] = str_replace("{pk}", $pk_name, $many_to_many["on"]);
					$many_to_many["on2"] = str_replace("{pk}", $pk_name, $many_to_many["on2"]);
					$many_to_many["on2"] = str_replace("{table.pk}", $this->get_pk_name($many_to_many['table']), $many_to_many["on2"]);
					$this->db->join($many_to_many["rel_table"], $many_to_many["on"], $join_type);
					$this->db->join($many_to_many["table"], $many_to_many["on2"], $join_type);
				}

				if (count($deep_joins)) {

					for ($i = 0; $i < count($deep_joins); $i++) {
                        $entity = $arg;
						$entity_name = $entity.'_model';
						$model = new $entity_name;
						$model->join($deep_joins[$i], 'deep', $join_type);
						$this->db->select($model->table . '.' . key($model->pk) . ' as _' . $entity . '_pk');
						
						if (array_key_exists($deep_joins[$i], $model->one_to_one))
						{
							$this->has_one[$entity] = $deep_joins[$i];
						} 
					}
					$entity = $deep_joins[0];
                    $entity_name = $entity.'_model';
                    $model = new $entity_name;
					$this->db->select($model->table . '.' . key($model->pk) . ' as _' . $entity . '_pk');
					$this->tables_rel[$entity] = $model->table;

				} elseif (!$deep_join) {
					$entity = $arg;
                    $entity_name = $entity.'_model';
                    $model = new $entity_name;
					$this->db->select($model->table . '.' . key($model->pk) . ' as _' . $entity . '_pk');
					$this->tables_rel[$entity] = $model->table;
				}
                unset($model);
			}
			$this->db->ar_join = array_unique($this->db->ar_join);
		} else {

			$this->bind_rel($args);
		}

		return $this;
	}


	/**
	 * prepare a where clause
	 * return $this, so you can use method chaining  
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @return \MY_Model
	 */
	public function where($field, $value) {

		foreach ($this->tables_rel as $id => $table) {
			$field = str_replace($id . '.', $table . '.', $field);
		}
		$this->db->where($field, $value);

		return $this;
	}

	
	/**
	 * prepare order by clause
	 * return $this, so you can use method chaining  
	 * 
	 * @param string $field
	 * @param string $direction should be 'asc' or 'desc'
	 * @return \MY_Model
	 */
	public function order_by($field, $direction) {

		foreach ($this->tables_rel as $id => $table) {
			$field = str_replace($id . '.', $table . '.', $field);
		}
		$this->db->order_by($field, $direction);

		return $this;
	}

	/**
	 * prepare limit clause
	 * 
	 * @param type $limit
	 * @param null $offset
	 * @return \MY_Model
	 */
	public function limit($limit, $offset = null) {
		$this->db->limit($limit, $offset = null);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * calling it after  model->select('model.field, othermodel.field, anothermodel.field')->join('othermodel/anothermodel')->get_grouped()
	 * it perform a "single query", with all needed joins, then return a  multidimensional array nesting fields in  model>othermodel>anothermodel structure
	 * 
	 * @return array
	 */
	public function get_grouped() {

		$this->db->ar_select = $this->fields_to_alias($this->db->ar_select);

		$result_grouped_arr = array();
		$result_arr = $this->get();

		$nested_arr = array();

		
		//  path = "vetrine/IDVETRINA/sedi/IDSESE/collaboratori/IDCOLLABORATORE"
		foreach($this->entities_grouped as $entities_arr)
		{
			array_unshift($entities_arr, $this->entity);


			$record_path_array = array();
			foreach ($result_arr as $entry) {


				$pk = array();
				$parsed_entry = $entry;

				// analizzando i _pk costruisco devo ricostruire qui il percorso 
				// vetrine/IDVETRINA/sedi/IDSEDE/comuni/IDCOMUNI"

				$record_path = '';

				$fields = array_keys($entry);
				foreach ($fields as $field) {

					if (preg_match("#^_(" . implode("|", $entities_arr) . ")_(\w+)$#", $field, $matches)) {

						$parsed_entry[$matches[2]] = $entry[$field];

						if ($matches[2] == 'pk') {
							
							if ($entry[$field] != '') {
								$record_path .= $matches[1] . "/" . $entry[$field] . "/";
								$record_path_array[] = $record_path;
								$pk[] = $entry; 
							}
						}
					}
					unset($parsed_entry[$field]);
				}
				foreach ($record_path_array as $path) {
					$sibiling = '';
					if (count($pk)>1)
					{
						$entity_path = preg_replace("#[0-9]+\/#", "", $path);
						$entity_arr  = explode('/', trim($entity_path, "/"));
						if (count($entity_arr)>1)
						{
							$entity_arr = array_reverse($entity_arr);
							$found = array_search($entity_arr[0], $this->has_one);

							if ($found && $found == $entity_arr[1])
							{	

								$sibiling = $entity_arr[0];
							}
						}


					}
					$this->nest_grouped($result_grouped_arr, trim($path, "/"), $entry, $sibiling); //$parsed_entry	
				}

				$paths[] = trim($record_path, "/");
			}

		}
		//debug($result_grouped_arr);
		return $result_grouped_arr;
	}


	// --------------------------------------------------------------------

	protected function nest_grouped(&$dest, $path, $entry, $sibiling='') {
		static $paths = array();


		if (in_array($path, $paths)) {
			return;
		} else {
			$paths[] = $path;
		}

		if ($sibiling!='')
		{
			//die($sibiling);
			$path = dirname(dirname($path));
			//debug($path);
			//debug($entry,true);
			
		}
		
		$entity = basename(dirname($path));
		if (!is_array($path))
			$path = explode('/', $path);



		
		$fields = array_keys($entry);
		foreach ($fields as $field) {
			
			
			if (preg_match("#^_(".$sibiling.")_(\w+)$#", $field, $matches)) {
				
				$parsed_entry[$sibiling.'.'.$matches[2]] = $entry[$field];
			} elseif (preg_match("#^_(" . $entity . ")_(\w+)$#", $field, $matches)) {
				$parsed_entry[$matches[2]] = $entry[$field];
			}

		}
		

		
		$a = &$dest;
		foreach ($path as $p) {
			if (!is_array($a))
				$a = array();
			$a = &$a[$p];
		}

		$a = $parsed_entry;
		
		return $a;
	}
		

	// --------------------------------------------------------------------

	protected function exec_preprocess_callback($action) {
		$this->preprocess_result = TRUE;
		if (isset($this->preprocess_callback[$action])) {
			$function = $this->preprocess_callback[$action];
			$arr_values = $function["arr_values"];
			(count($arr_values) > 0) ? array_unshift($arr_values, $this) : $arr_values = array($this);
			if ($function["object"] != '')
				$this->preprocess_result = call_user_func_array(array($function["object"], $function["name"]), $arr_values);
			else
				$this->preprocess_result = call_user_func_array($function["name"], $arr_values);

			return $this->preprocess_result;
		}
	}
	// --------------------------------------------------------------------

	protected function exec_postprocess_callback($action) {
		if (isset($this->postprocess_callback[$action])) {
			$function = $this->postprocess_callback[$action];
			$arr_values = $function["arr_values"];
			(count($arr_values) > 0) ? array_unshift($arr_values, $this) : $arr_values = array($this);

			$this->action = $action;
			if ($function["object"] != '')
				$this->postprocess_result = call_user_func_array(array($function["object"], $function["name"]), $arr_values);
			else
				$this->postprocess_result = call_user_func_array($function["name"], $arr_values);


			return $this->postprocess_result;
		}
	}
	// --------------------------------------------------------------------

	protected function bind_data($data) {
		$this->data = $data;
	}

	// --------------------------------------------------------------------
	//todo "dinamica"customizabile" la "on2" nella many to many
	protected function bind_rel($args = array()) {
		if (count($this->pk) > 1)
			return;

		reset($this->pk);
		list($pk_name, $pk_value) = each($this->pk);
		$table_dot_pk = $this->table . "." . $pk_name;
		$where = array($table_dot_pk => $pk_value);

		foreach ($args as $arg) {
			$one_to_one = array();
			$one_to_many = array();
			$many_to_many = array();

			if (array_key_exists($arg, $this->one_to_one)) {
				$one_to_one = $this->one_to_one[$arg];
				$this->db->select($one_to_one["table_alias"] . ".*");
				$this->db->from($this->table);

				$one_to_one["on"] = str_replace("{pk}", $pk_name, $one_to_one["on"]);
				$this->db->join($one_to_one["table"], $one_to_one["on"]);
				$this->db->where($where);
				$this->db->get();

				if ($this->db->num_rows() > 0) {
					$results = $this->db->result_array();
					$this->data_rel[$one_to_one["id"]] = $results[0];
				}
			} elseif (array_key_exists($arg, $this->one_to_many)) {

				$one_to_many = $this->one_to_many[$arg];
				$this->db->select($one_to_many["table_alias"] . ".*");
				$this->db->from($this->table);

				$one_to_many["on"] = str_replace("{pk}", $pk_name, $one_to_many["on"]);
				$this->db->join($one_to_many["table"], $one_to_many["on"]);
				$this->db->where($where);
				$this->db->get();
				if ($this->db->num_rows() > 0) {
					$this->data_rel[$one_to_many["id"]] = $this->db->result_array();
				}
			} elseif (array_key_exists($arg, $this->many_to_many)) {

				$many_to_many = $this->many_to_many[$arg];

				$this->db->select($many_to_many["table_alias"] . ".*");
				$this->db->from($this->table);
				$many_to_many["on"] = str_replace("{pk}", $pk_name, $many_to_many["on"]);
				$many_to_many["on2"] = str_replace("{pk}", $pk_name, $many_to_many["on2"]);
				$many_to_many["on2"] = str_replace("{table.pk}", $this->get_pk_name($many_to_many['table']), $many_to_many["on2"]);
				$this->db->join($many_to_many["rel_table"], $many_to_many["on"]);
				$this->db->join($many_to_many["table"], $many_to_many["on2"]);
				$this->db->where($where);
				$this->db->get();
				if ($this->db->num_rows() > 0) {
					$this->data_rel[$many_to_many["id"]] = $this->db->result_array();
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	protected function show_error($error_arr) {
		echo '<p>' . implode('</p><p>', (!is_array($error_arr)) ? array($error_arr) : $error_arr) . '</p>';
	}
	
	// --------------------------------------------------------------------
	
	protected function fields_to_alias($fields) {
		$match = implode('|', array_values($this->tables_rel));
		$alias = array();
		foreach ($fields as $field) {
			$alias[] = preg_replace_callback("#^($match).(\w+)$#i", array($this, 'callback_fields_to_alias'), $field);
		}
		return $alias;
	}

	// --------------------------------------------------------------------

	protected function callback_fields_to_alias($matches) {
		$entity = array_search($matches[1], $this->tables_rel);
		if ($entity) {

			return $matches[0] . ' as _' . $entity . '_' . strtolower($matches[2]);
		}
		return $matches[0];
	}

}
