<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class field_field extends component_library {

	//main properties
	public $type = "field";
	public $label;
	public $name;
	public $attributes = array();
	public $output = "";
	public $visible = true;
	public $extra_output = "";
	public $before_output = "";

	public $serialization_sep = '|';

	//atributes
	public $maxlength;
	public $onclick;
	public $onchange;
	public $style;


	//datafilter related
	public $operator = "";
	public $clause = "like";
	public $orvalue = "";
    
	//field actions & field status
	public $mode = 'editable';  //editable, readonly, autohide
	public $apply_rules = true;
	public $required = false;

	//data settings
	public $model;  //dataobject model
	public $insert_value = null; //default value for insert
	public $update_value = null; //default value for update
        public $show_value = null; //default value in visualization
	public $options = array(); //associative&multidim. array ($value => $description)
	public $mask = null;
	public $group;

	public $value = null;
	public $values = array();
	public $new_value;

	public $request_refill = true;
	public $is_refill  = false;

	public $options_table = '';

	// layout
	public $layout = array(
		'field_separator'  => '<br />',
		'option_separator' => '',
		'null_label' => '[null]',
	);
	public $star = '';


	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	// --------------------------------------------------------------------

	public function set_name($name)
	{
		//replace dots with underscores so field names are html/js friendly
		$this->name = str_replace(array(".",",","`"), array("_","_","_"),$name);
        
		if (!isset($this->db_name)) $this->db_name = $name;
	}

	// --------------------------------------------------------------------

	public function set_group($group)
	{

		$this->group = $group;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_onchange($onchange)
	{

		$this->onchange = $onchange;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_rule($rule)
	{
		//keep CI/kohana serialization
		if(is_array($rule)) $rule = join('|',$rule);
		$this->rule = $rule;
		if ((strpos($this->rule,"required")!==false) AND !isset($this->no_star) )
		{
			$this->required = true;
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_mode($mode)
	{

		$this->mode = $mode;
		return $this;
	}
	
	// --------------------------------------------------------------------

	public function set_mask($mask)
	{

		$this->mask = $mask;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_in($in)
	{
		$this->in = $in;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_clause($clause)
	{
		$this->clause = $clause;
		return $this;
	}
	
	// --------------------------------------------------------------------

	public function set_operator($operator)
	{
		$this->operator = $operator;
		return $this;
	}
	// --------------------------------------------------------------------

	public function set_attributes($attributes)
	{

		$this->attributes = $attributes;
		return $this;
	}

	// --------------------------------------------------------------------
	
	public function set_style($style)
	{
		if (isset($this->attributes['style']))
		{
			$this->attributes['style'] = $this->attributes['style'].';'.$style;
		} else {
			$this->attributes['style'] = $style;
		}
		return $this;
	}
	
	// --------------------------------------------------------------------


	
	public function set_before($before)
	{
		$this->before_output = $before;
		return $this;
	}
	
	// --------------------------------------------------------------------

	public function set_insert_value($insert_value)
	{
		$this->insert_value = $insert_value;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_update_value($update_value)
	{
		$this->update_value = $update_value;
		return $this;
	}

	// --------------------------------------------------------------------
	public function set_pattern($pattern)
	{
		$this->pattern = $pattern;
		return $this;
	}

	// --------------------------------------------------------------------
	
	public function set_extra($extra)
	{
		$this->extra_output = $extra;
		return $this;
	}
	
	// --------------------------------------------------------------------

	//http://svn.bitflux.ch/repos/public/popoon/trunk/classes/externalinput.php
	function xssfilter($string)
	{
		if (is_array($string))
		{
			return $string;
		}
		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}
		if ($this->type=="html")
		{
			return $string;
		}
		$string = str_replace(array("&amp;","&lt;","&gt;"),array("&amp;amp;","&amp;lt;","&amp;gt;",),$string);
		// fix &entitiy\n;

		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u',"$1;",$string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu',"$1$2;",$string);
		$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu',"$1>",$string);
		// remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2nojavascript...',$string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu','$1=$2novbscript...',$string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#Uu','$1=$2nomozbinding...',$string);
		//<span style="width: expression(alert('Ping!'));"></span>
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU',"$1>",$string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu',"$1>",$string);
		//remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i',"",$string);
		//remove really unwanted tags

		do {
				$oldstring = $string;
				$string = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$string);
		} while ($oldstring != $string);

		return $string;
	}


	// --------------------------------------------------------------------

	public function get_value()
	{
		if (($this->request_refill == true) && isset($_POST[$this->name]))
		{
			//serializzazione o gestione della relazione 1-n o n-m
			if (is_array($_POST[$this->name]))
			{
				$values = array();
				foreach ($_POST[$this->name] as $value)
				{
					if (get_magic_quotes_gpc()) $values[] = stripslashes($value);
				}
				$this->value = implode($this->serialization_sep,$values);
			}
			else
			{
				$request_value = self::xssfilter($_POST[$this->name]);
				if (get_magic_quotes_gpc()) $request_value = stripslashes($request_value);
				$this->value = $request_value;
			}
			$this->is_refill = true;
		} elseif (($this->status == "create") && ($this->insert_value != null))
		{
			$this->value = $this->insert_value;
		} elseif (($this->status == "modify") && ($this->update_value != null))
		{
			$this->value = $this->update_value;
		} elseif (($this->status == "show") && ($this->show_value != null))
		{
			$this->value = $this->show_value;
		}
		elseif ( (isset($this->model)) && ($this->model->loaded) && (!isset($_POST[$this->name])) && (isset($this->db_name)) )
		{
		
			$name = $this->db_name;
			$path = explode('.', $name);
			if (count($path)==3 && $this->model->has_rel($path[0]))
			{
				$v = $this->model->get_rel($path[0],$path[1]); 
				$this->value = $v[$path[2]];
			}
			elseif ($this->options_table!="")
			{
				//da ottimizzare, (options_array dovrebbe accettare anche uncampo solo)
				rpd::$db->query('SET @rownum:=-1;');
				rpd::$db->select('@rownum:=@rownum+1 rownum,'.$this->name);
				rpd::$db->from($this->options_table);
				rpd::$db->where($this->model->pk);
				rpd::$db->get();
				$values = rpd::$db->options_array();
				$this->value = implode($this->serialization_sep,$values);
			}
			else
			{
				$this->value = $this->model->get($name);
			}
		}

		$this->get_mode();
	}

	// --------------------------------------------------------------------

	public function get_new_value()
	{
		if (isset($_POST[$this->name]))
		{
			if ($this->status == "create")
			{
				$this->action = "insert";
			} elseif ($this->status == "modify"){
				$this->action = "update";
			}

			//serializzazione o gestione della relazione 1-n o n-m
			if (is_array($_POST[$this->name]))
			{
				$values = array();
				foreach ($_POST[$this->name] as $value)
				{
					$values[] = (get_magic_quotes_gpc()) ? stripslashes(self::xssfilter($value)) : self::xssfilter($value);
				}
				$this->new_value = implode($this->serialization_sep,$values);
			}
			else
			{
				$request_value = self::xssfilter($_POST[$this->name]);
				if (get_magic_quotes_gpc()) $request_value = stripslashes($request_value);
				$this->new_value = $request_value;
			}

		} elseif( ($this->action == "insert") && ($this->insert_value != null)) {
			$this->new_value = $this->insert_value;
		} elseif( ($this->action == "update") && ($this->update_value != null)) {
			$this->new_value = $this->update_value;
		} else {
			$this->action = "idle";
		}
	}

	// --------------------------------------------------------------------

	public function get_mode()
	{
		switch ($this->mode)
		{
			case "showonly":
				if (($this->status != "show"))
				{
                                    $this->status = "hidden";
                                }
				break;
			case "autohide":
				if (($this->status == "modify")||($this->action == "update"))
				{
					$this->status = "show";
					$this->apply_rules = false;
				}
				break;

			case "readonly":
				$this->status = "show";
				$this->apply_rules = false;
				break;

			case "optionalupdate":
				if ($this->action == "update")
				{
					if(!isset($_POST[$this->name."CheckBox"]))
					{
						$this->apply_rules = false;
					}
				}
				break;

			case "autoshow":
				if (($this->status == "create")||($this->action == "insert"))
				{
					$this->status = "hidden";
					$this->apply_rules = false;
				}
				break;
			case "hidden":
					$this->status = "hidden";
					$this->apply_rules = false;
				break;
			case "show":
				break;

			default:;
		}

		if (isset($this->when))
		{
			if (is_string($this->when) AND strpos($this->when, '|'))
			{
				$this->when = explode('|', $this->when);
			}
			$this->when = (array) $this->when;
			if (!in_array($this->status, $this->when) AND !in_array($this->action, $this->when))
			{
				$this->visible = false;
				$this->apply_rules = false;
			}
			else
			{
				$this->visible = true;
				$this->apply_rules = true;
			}
		}
	}

	// --------------------------------------------------------------------

	public function set_options($options)
	{
		if (is_array($options))
		{
			$this->options += $options;
		}
		elseif (strpos(strtolower($options),"select")!==FALSE)
		{
			rpd::$db->query($options);
			$result = rpd::$db->result_array();
			foreach ($result as $row)
			{
				$values = array_values($row);
				$this->set_option($values[0], $values[1]);
				if (count($values)==4)
				{
					$this->set_option_group($values[0], $values[1], $values[2], $values[3]);
				}
			}
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_option($value='', $description='')
	{
		$this->options[$value] = $description;
		return $this;
	}
	// --------------------------------------------------------------------
	public function set_option_group($value='', $description='', $group_id='', $group_label='')
	{
		//$this->options[$value] = $description;
		$this->option_groups[$group_id]['label'] = $group_label;
		$this->option_groups[$group_id]['options'][$value] = $description;
		return $this;
	}
	// --------------------------------------------------------------------

	public function auto_update($save=false)
	{
		$this->get_value();
		$this->get_new_value();

		if (is_object($this->model)&& isset($this->db_name) && $this->options_table=="")
			{
			if (!in_array($this->db_name, $this->model->field_names))
			{
				$path = explode('.', $this->db_name);
				if (count($path)==3 && $this->model->has_rel($path[0]))
				{
				$v = $this->model->set_rel($path[0],$path[2], $this->new_value); 
				$this->value = $v[$path[2]];
				}
				return true;
			}
				if (isset($this->new_value))
				{
					$this->model->set($this->db_name, $this->new_value);
				} else {
					$this->model->set($this->db_name, $this->value);
				}
				if($save)
				{
					return $this->model->save();
				}
		}

		if ($this->options_table!="")
		{
				$this->connect();
			//bisogna farla diventare "post_process" e creare una procedure per eliminare i figli in cancellazione,
			$this->db->delete($this->options_table, $this->model->pk);

			$values = explode('|',$this->new_value);
			foreach ($values as $value)
			{
				$set = $this->model->pk;
				$set[$this->name] = $value;
				$this->db->insert($this->options_table, $set);
				unset($set);
			}
		}
		return true;
	}

	// --------------------------------------------------------------------

	public function extra_output()
	{
		return '<span class="extra">'.$this->extra_output.'</span>';
	}
	
	// --------------------------------------------------------------------

	public function before_output()
	{
		return '<span class="extra">'.$this->before_output.'</span>';
	}

	// --------------------------------------------------------------------

	public function build()
	{
		$output = "";
		$this->get_value();
		$this->star = (!$this->status_is("show") AND $this->required) ? '&nbsp;*' : '';

		$attributes = array('onchange','name','type','size','style','class','rows','cols');

		foreach ($attributes as $attribute)
		{
			if (isset($this->$attribute))
			$this->attributes[$attribute] = $this->$attribute;
		}
		if (!isset($this->attributes['id']))
			$this->attributes['id'] = $this->name;
		if (isset($this->css_class))
			$this->attributes['class'] = $this->css_class;

		if ($this->visible === false)
		{
			return false;
		}
	}

}
