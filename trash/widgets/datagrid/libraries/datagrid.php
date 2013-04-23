<?php

if (!defined('CORE_PATH'))
	exit('No direct script access allowed');

/**
 * Datagrid library
 * 
 * @package    Core
 * @author     Felice Ostuni
 * @copyright  (c) 2011 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class datagrid_library extends dataset_library
{

	protected $fields = array();
	public $columns = array();
	public $actions = array();
	public $rows = array();
	public $checkbox_form = false;
	public $output = "";
	public $add_url = "";
	public $add_hash = "";

	/**
	 * internal, it build append columns, called only if $config is passed to the constructor
	 * 
	 * @param type $fields 
	 */
	protected function set_columns($columns)
	{
		foreach ($columns as $column)
		{
			$this->set_column($column);
		}
	}

	/**
	 * called automagically by column($column) or cplumn($pattern, $label, $orderby)
	 * where pattern is fieldname or a string containing html/placeholders/formatting tags like:
	 * "<ucfirst>{firstnamename}</ucfirst> {lastname}"
	 * 
	 * @return object column object  
	 */
	public function set_column()
	{
		$column = array();
		if (func_num_args() == 3)
		{
			list($column['pattern'], $column['label'], $column['orderby']) = func_get_args();
		}
		if (func_num_args() == 2)
		{
			list($column['pattern'], $column['label']) = func_get_args();
		}
		if (func_num_args() == 1)
		{
			$column = func_get_arg(0);
		}
		//share source with columns
		if (!isset($column['source']))
			$column['source'] = $this->source;
		//detect if is a checkbox column
		if (isset($column['checkbox']))
			$this->checkbox_form = true;
		$column = new datagrid_column($column);
		$this->columns[] = $column;
		return $column;
	}

	
	function action_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.delete');
		$action_name = (isset($config['name'])) ? $config['name'] : 'delete';
		$action = "javascript:document.forms['grid" . $this->cid . "'].grid_action.value='" . $action_name . "';document.forms['grid" . $this->cid . "'].submit()";
		$this->button("btn_" . $action_name, $caption, $action, "TR");
	}

	// --------------------------------------------------------------------
	function add_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.add');
		$url = null;
		if (isset($config['url']) OR $this->add_url != "")
		{
			$url = (isset($config['url'])) ? $config['url'] : $this->add_url;
		}
		$url = url_helper::append('create' . $this->cid, 1, $url);
		if (isset($config['hash']) OR $this->add_hash != "")
		{
			$url .= '#' . $this->add_hash;
		}
		$action = "javascript:window.location.href='" . $url . "'";
		$this->button("btn_add", $caption, $action, "TR");
	}

	/**
	 * detect current dataform "action" checking current url
	 */
	protected function sniff_action()
	{
		if (isset($_POST['grid_action']))
		{
			$action = $_POST['grid_action'];
			if (isset($this->actions[$action]))
				call_user_func($this->actions[$action]);
		}
	}

	// --------------------------------------------------------------------
	protected function build_grid()
	{
		html_helper::css('widgets/datagrid/assets/datagrid.css');
		$data = get_object_vars($this);
		$this->build_buttons();
		if ($this->checkbox_form)
		{
			rpd::load('helper', 'form');
			$attributes = array('class' => 'form', 'name' => 'grid');
                        $url = ($this->url != '') ? $this->url : url_helper::get_url();
			$data['form_begin'] = form_helper::open($url, $attributes);
			$data['form_end'] = form_helper::close();
			$data['hidden'] = form_helper::hidden('grid_action', 'true');
		} else
		{
			$data['hidden'] = "";
			$data['form_begin'] = "";
			$data['form_end'] = "";
		}
		$data['container'] = $this->button_containers();
		//table rows
		foreach ($this->data as $tablerow)
		{
			unset($row);
			foreach ($this->columns as $column)
			{
				unset($cell);
				$column->reset_pattern();
				$column->set_row($tablerow);
				$cell = get_object_vars($column);
				$cell["value"] = $column->get_value();
				$cell["type"] = $column->column_type;
				$row[] = $cell;
			}
			$data["rows"][] = $row;
		}
		$data["pagination"] = $this->pagination;
		$data["total_rows"] = $this->total_rows;
		return rpd::view('datagrid', $data);
	}

	// --------------------------------------------------------------------
	protected function build_excel()
	{
		$filename = $this->label . ".xls";
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: inline; filename=$filename");
		$data = get_object_vars($this);
		//table rows
		foreach ($this->data as $tablerow)
		{
			unset($row);
			foreach ($this->columns as $column)
			{
				unset($cell);
				$column->reset_pattern();
				$column->set_row($tablerow);
				$cell = get_object_vars($column);
				$cell["value"] = $column->get_value();
				$cell["type"] = $column->column_type;
				$row[] = $cell;
			}
			$data["rows"][] = $row;
		}
		$data["total_rows"] = $this->total_rows;
		return rpd::view('datagrid_excel', $data);
	}

	// --------------------------------------------------------------------
	protected function build_csv()
	{

		$output = '';
		$filename = preg_replace('/[^0-9a-z\_\-]/i', '', $this->label) . ".csv";
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=" . $filename);

		$data = get_object_vars($this);
		foreach ($this->columns as $column)
		{
			$labels[] = $column->label;
		}
		$output.= '"' . implode('";"', $labels) . '"' . "\n";
		//rows
		foreach ($this->data as $tablerow)
		{
			unset($values);
			foreach ($this->columns as $column)
			{
				$column->reset_pattern();
				$column->set_row($tablerow);
				$values[] = str_replace('"', '""', strip_tags($column->get_value())); //quota "  come "" (notazione excel)
			}
			$rows[] = '"' . implode('";"', $values) . '"';
		}
		//$output.= implode("\n", $rows) . "\n";
		echo $output.= implode("\n", $rows) . "\n";
		die;
		//return mb_convert_encoding($output, 'iso-8859-1', 'utf-8');
	}

	// --------------------------------------------------------------------
	public function build($method = 'grid')
	{
		parent::build();
		//sniff and perform action
		$this->sniff_action();
		foreach ($this->columns as & $column)
		{
			if (isset($column->orderby))
			{
				$column->orderby_asc_url = $this->orderby_link($column->orderby, 'asc');
				$column->orderby_desc_url = $this->orderby_link($column->orderby, 'desc');
			}
		}
		$method = 'build_' . $method;
		$this->output = $this->$method();
	}

}

/**
 * Datagrid column
 * 
 * @package    Core
 * @author     Felice Ostuni
 * @copyright  (c) 2011 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class datagrid_column extends widget_library
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
	protected function set_url($url, $img = '', $onclick='')
	{
		$uri = rpd::uri($url);
		$this->url = rpd::url($uri);
		//$this->url = $url;
		$this->img = $img;
		$this->onclick = $onclick;
		return $this;
	}

	// --------------------------------------------------------------------
	public function set_attributes($attributes)
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
