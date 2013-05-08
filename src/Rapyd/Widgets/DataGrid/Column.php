<?php


namespace Rapyd\Widgets\DataGrid;



class Column extends Widget
{

	public $url = "";
	public $link = "";
	public $onclick = "";
	public $label = "";
	public $attributes = array();
	public $tr_attributes = array();
	public $tr_attr = '';
	public $column_type = "normal"; //orderby, detail, ation
	public $orderby = false;
	public $checkbox = "";
	public $check = "";
	public static $checkbox_id = 1;
	public $orderby_asc_url;
	public $orderby_desc_url;
	protected $pattern = "";
	protected $pattern_type = null;
	protected $field = null;
	protected $field_name = null;
	protected $field_list = array();

	// --------------------------------------------------------------------
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->check_pattern();
	}

	// --------------------------------------------------------------------
	protected function set_pattern($pattern)
	{
		$this->pattern = is_object($pattern) ? clone ($pattern) : $pattern;
		$this->rpattern = is_object($pattern) ? clone ($pattern) : $pattern;
	}

	// --------------------------------------------------------------------
	protected function check_pattern()
	{
		if (is_object($this->pattern))
		{
			$this->pattern_type = "field_object";
			$this->field = $this->pattern;
			if ($this->orderby === true)
			{
				$this->orderby = $this->field->name;
			}
		} else
		{
			$this->field_list = parent::parse_pattern($this->pattern);
			if (is_array($this->field_list))
			{
				$this->pattern_type = "pattern";
				if ($this->orderby === true)
				{
					$this->orderby = $this->field_list[0];
				}
			} else
			{
				$this->pattern_type = "field_name";
				$this->field_name = $this->pattern;
				if ($this->orderby === true)
				{
					$this->orderby = (isset($this->orderby_field)) ? $this->orderby_field : $this->field_name;
				}
			}
		}
		if ($this->orderby)
		{
			$this->column_type = 'orderby';
		}
	}

	// --------------------------------------------------------------------
	function reset_pattern()
	{
		$this->rpattern = $this->pattern;
	}

	// --------------------------------------------------------------------
	protected function setUrl($url, $img = '', $onclick='')
	{
		$uri = rpd::uri($url);
		$this->url = rpd::url($uri);
		//$this->url = $url;
		$this->img = $img;
		$this->onclick = $onclick;
		return $this;
	}

	// --------------------------------------------------------------------
	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
		return $this;
	}

	// --------------------------------------------------------------------
	public function set_callback($callback, $object = null)
	{
		$this->callback = $callback;
		$this->callback_object = $object;
	}

	// --------------------------------------------------------------------
	public function set_tr_attributes($attributes)
	{
		$this->tr_attributes = $attributes;
		return $this;
	}

	// --------------------------------------------------------------------
	public function set_row($data_row)
	{
		switch ($this->pattern_type)
		{
			case "field_object":
				if (isset($data_row[$this->field->name]))
				{
					$this->field->value = $data_row[$this->field->name];
				} else
				{
					$this->field->value = "";
				}
				break;
			case "pattern":
				$this->rpattern = $this->replace_pattern($this->rpattern, $data_row);
				break;
			case "field_name":
				if (isset($data_row[$this->field_name]))
				{
					$this->rpattern = $data_row[$this->field_name];
				} elseif (array_key_exists($this->field_name, $data_row))
				{
					$this->rpattern = "";
				}
				break;
		}

		if (isset($this->callback_object))
		{
			$this->rpattern = call_user_func(array($this->callback_object, $this->callback), $data_row);
		} elseif (isset($this->callback))
		{
			$this->rpattern = call_user_func($this->callback, $data_row);
		}
		if ($this->url)
		{
			if (!isset($this->attributes['style']))
				$this->attributes['style'] = 'width: 70px; text-align:center; padding-right:5px';
			$this->link = parent::replace_pattern($this->url, $data_row);
		}
		if ($this->checkbox != "")
		{
			$value = $data_row[$this->field_name];
			$attributes = array('name' => $this->field_name . '[]', 'id' => $this->field_name . (string) self::$checkbox_id++,);
			$this->check = form_helper::checkbox($attributes, $value);
		}
		$this->attributes = html_helper::attributes($this->attributes);
		
		if (count($this->tr_attributes))
		{
			$this->tr_attr = array();
			foreach($this->tr_attributes as $k=>$v)
			{
				$this->tr_attr[$k] = parent::replace_pattern($v, $data_row);
			}
			$this->tr_attr = html_helper::attributes($this->tr_attr);
		}
	}

	/**
	 * a column value by default is a string: the field-name you want to show in the column
	 * but it support also a "pattern" with html and placeholders like : {field1} <br /> {field2}
	 * @return type 
	 */
	function get_value()
	{
		switch ($this->pattern_type)
		{
			case "field_object":
				$this->field->request_refill = false;
				$this->field->status = "show";
				$this->field->build();
				return $this->field->output;
				break;
			case "pattern":

				if ($this->rpattern == "")
				{
					$this->rpattern = "&nbsp;";
				}
				$this->rpattern = parent::replace_functions($this->rpattern);
				return $this->rpattern;
				break;
			case "field_name":
				$this->rpattern = nl2br(htmlspecialchars($this->rpattern));
				if ($this->rpattern == "")
				{
					$this->rpattern = "&nbsp;";
				}
				return $this->rpattern;
				break;
		}
	}
	
	/**
	 * replace {field} with value 
	 * @return string
	 */
	function orderby_link()
	{
		return str_replace('{field}', $column->orderby_field, $this->orderby_asc_url);
	}

}
