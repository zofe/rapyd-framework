<?php

if (!defined('CORE_PATH'))
	exit('No direct script access allowed');



class dataform_library extends widget_library
{

	public $model;
	public $output = "";
	protected $source;
	public $fields = array();
	public $hash = "";
	protected $errors = array();
	//form action, enctype, scripts
	protected $process_url = "";
	protected $multipart = false;
	protected $default_group;
	public $attributes = array('class' => 'form');
	protected $error_string;
	protected $form_scripts;
	
	/**
	 * it get an identifier, instance a validation library and check if a model is passed (in the config-array)
	 * 
	 * @param array $config 
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->cid = parent::get_identifier();
		$this->validation = new rpd_validation_library();
                $url = ($this->url != '') ? $this->url : url_helper::get_url();
		$this->process_url = url_helper::append('process', 1, $url);
		if (isset($this->model))
		{
			$this->status = "create";
			if (isset($this->model))
			{
				$this->status = "create";
			}
		}
	}

	/**
	 * internal, it build fields, called only if $config is passed to the constructor
	 * 
	 * @param type $fields 
	 */
	protected function set_fields($fields)
	{
		foreach ($fields as $field)
		{
			$this->set_field($field);
		}
	}

	/**
	 * called automagically by field($field) or field($type, $name, $label)
	 * 
	 * @return object field class  
	 */
	public function set_field()
	{
		$field = array();
		if (func_num_args() == 3)
		{
			list($field['type'], $field['name'], $field['label']) = func_get_args();
		}
		if (func_num_args() == 1)
		{
			$field = func_get_arg(0);
		}
		if (isset($field['field']))
		{
			list($field['type'], $field['name'], $field['label']) = explode('|', $field['field']);
		}
		$field_name = $field["name"];
		//load and instance field
		$field_file = strtolower($field["type"]);
		$field_class = $field_file . '_field';
		$field_obj = new $field_class($field);
		if ($field_obj->type == "upload")
		{
			$this->multipart = true;
			if (!isset($this->upload))
			{
				$this->upload = new upload_helper();
			}
			$field_obj->upload = $this->upload;
		}
		//share model
		if (isset($this->model))
		{
			$field_obj->model = $this->model;
		}
		//default group
		if (isset($this->default_group) && !isset($field_obj->group))
		{
			$field_obj->group = $this->default_group;
		}
		$this->fields[$field_name] = $field_obj;
		return $field_obj; //method chaining
	}

	public function &get_field($field_name)
	{
		if (isset($this->fields[$field_name]))
		{
			return $this->fields[$field_name];
		}else {
			$this->show_error('datamodel non valido ' . get_class($source));
			die();
		}
	}
	
	/**
	 * set css style
	 * @todo search online for a better way to work with html/css for example using a dom-api like phpquery  
	 * @param string $style 
	 */
	protected function set_style($style)
	{
		$this->set_attributes(array('style' => $style));
	}

	/**
	 * source can be a a table-name or a database-model
	 * if you pass a table-name a datamodel will be instanced
	 * if the form already contains fields the model will be shared with each field
	 * 
	 * @param mixed $source
	 * @return object datamodel 
	 */
	public function set_source($source)
	{
		//instance or reuse a model
		if (is_object($source) and (get_class($source) == 'rpd_datamodel_model' OR is_subclass_of($source, "rpd_datamodel_model")))
		{
			$this->model = $source;
		} elseif (is_string($source))
		{
			$this->model = new rpd_datamodel_model($source);
		} else
		{

			$this->show_error('datamodel non valido ' . get_class($source));
			die();
		}
		if (count($this->fields))
		{
			foreach ($this->fields as $field_obj)
			{
				if (in_array($field_obj->name, $this->model->field_names()))
				{
					$field_obj->model = $this->model;
				}
			}
		}
		$this->validation->model = $this->model;
		return $this->model;
	}

	/**
	 * shortcut for model->load
	 * 
	 * @param mixed $id 
	 */
	public function load($id)
	{
		if (isset($this->model))
		{
			$this->model->load($id);
		}
	}

	/**
	 * internal, build each field
	 * 
	 */
	public function build_fields()
	{
		foreach ($this->fields as $field)
		{
			//share status
			$field->status = $this->status;
			$field->build();
		}
	}

	/**
	 * usage:
	 * <code>
	 * $edit->pre_process(array('update'), array($this, 'some_method'));
	 * ..
	 * function some_method($model)
	 * {
	 *	 //do checks.. etc..
	 *	 $model->set('afield', 'avalue');
	 * }
	 * </code>
	 * 
	 * @todo replace/rename it using ->callback() or something similar
	 * @param type $action
	 * @param type $function
	 * @param type $arr_values 
	 */
	public function pre_process($action, $function, $arr_values = array())
	{
		$this->model->pre_process($action, $function, $arr_values);
	}

	/**
	 * usage:
	 * <code>
	 * $edit->post_process(array('insert'), array($this, 'some_method'));
	 * ..
	 * function some_method($model)
	 * {
	 *	 //do checks.. etc..
	 *	 $model->set('afield', 'avalue');
	 * }
	 * </code>
	 * 
	 * @param type $action
	 * @param type $function
	 * @param type $arr_values 
	 */
	public function post_process($action, $function, $arr_values = array())
	{
		$this->model->post_process($action, $function, $arr_values);
	}

	/**
	 * internal, build each field then form
	 * 
	 * @return string compiled form 
	 */
	protected function build_form()
	{
		html_helper::css('widgets/dataform/assets/dataform.css');
		$data = get_object_vars($this);
		$data['container'] = $this->button_containers();
		$form_type = 'open';
		// See if we need a multipart form
		foreach ($this->fields as $field_obj)
		{
			if ($field_obj instanceof upload_field)
			{
				$form_type = 'open_multipart';
				break;
			}
		}
		// Set the form open and close
		if ($this->status_is('show'))
		{
			$data['form_begin'] = '<div class="form">';
			$data['form_end'] = '</div>';
		} else
		{
			$data['form_begin'] = form_helper::$form_type($this->process_url, $this->attributes);
			$data['form_end'] = form_helper::close();
		}
		$data['fields'] = $this->fields;
		return rpd::view('dataform', $data);
	}

	/**
	 * main method it detect status, exec action and build output
	 * 
	 * @param string $method 
	 */
	public function build($method = 'form')
	{
		$this->process_url = $this->process_url . $this->hash;
		//detect form status (output)
		if (isset($this->model))
		{
			$this->status = ($this->model->loaded) ? "modify" : "create";
		} else
		{
			$this->status = "create";
		}
		//build fields
		$this->build_fields();
		//process only if instance is a dataform
		if (is_a($this, 'dataform_library'))
		{
			//build buttons
			$this->build_buttons();
			//sniff action
			if (isset($_POST) && (url_helper::value('process')))
			{
				$this->action = ($this->status == "modify") ? "update" : "insert";
			}
			//process
			$this->process();
		}
		$method = 'build_' . $method;
		$this->output = $this->$method();
	}

	/**
	 * internal, run form validation and return boolean result
	 * 
	 * @return bool 
	 */
	protected function is_valid()
	{
		//some fields mode can disable or change some rules.
		foreach ($this->fields as $field)
		{
			$field->action = $this->action;
			$field->get_mode();
			if (isset($field->rule))
			{
				if (($field->type != "upload") && $field->apply_rules)
				{
					$fieldnames[$field->name] = $field->label;
					$rules[$field->name] = $field->rule;
				} else
				{
					$field->required = false;
				}
			}
		}
		if (isset($rules))
		{
			$this->validation->set_rules($rules);
			$this->validation->set_fields($fieldnames);
			if (count($_POST) < 1)
			{
				$_POST = array(1);
			}
		} else
		{
			return true;
		}
		$result = $this->validation->run();
		$this->error_string = $this->validation->error_string;
		return $result;
	}

	/**
	 * internal, process form, it there is a model try to save data
	 * 
	 * 
	 * @todo redirect after profess are all in javascript (window.location), this is really bad, but there is some reason i don't remember
	 * @todo move language specific messages to i18n files
	 * @return boolean 
	 */
	protected function process()
	{
		//database save
		switch ($this->action)
		{
			case "update":
			case "insert":
				//validation failed
				if (!$this->is_valid())
				{
					$this->process_status = "error";
					foreach ($this->fields as $field)
					{
						$field->action = "idle";
					}
					return false;
				} else
				{
					$this->process_status = "success";
				}
				foreach ($this->fields as $field)
				{
					$field->action = $this->action;
					$result = $field->auto_update();
					if (!$result)
					{
						$this->process_status = "error";
						$this->error_string = $field->save_error;
						return false;
					}
				}
				if (isset($this->model))
				{
					$return = $this->model->save();
				} else
				{
					$return = true;
				}
				if (!$return)
				{
					if ($this->model->preprocess_result === false)
					{
						if ($this->action_is("update"))
						{
							$this->error_string.= $this->model->error_string;
						} else
						{
							$this->error_string.= $this->model->error_string;
						}
					}
					$this->process_status = "error";
				}
				return $return;
				break;
			case "delete":
				$return = $this->model->delete();
				if (!$return)
				{
					if ($this->model->preprocess_result === false)
					{
						$this->error_string.= $this->model->error_string;
					}
					$this->process_status = "error";
				} else
				{
					$this->process_status = "success";
				}
				break;
			case "idle":
				$this->process_status = "show";
				return true;
				break;
			default:
				return false;
		}
	}

	/**
	 * internal, add a submit button
	 * 
	 * @param array $config 
	 */
	public function save_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.save');
		$this->submit("btn_submit", $caption, "BL");
	}

	/**
	 * internal, used to join in a single string all error messages
	 * 
	 * @param array $message 
	 */
	protected function show_error($message)
	{
		echo '<p>' . implode('</p><p>', (!is_array($message)) ? array($message) : $message) . '</p>';
	}

	/**
	 * nest a content inside a form, it can be called onlt after a build() bacause it work on compiled output
	 * 
	 * @param string $field_id id of container
	 * @param string $content content to be nested
	 */
	public function nest($field_id, $content)
	{
		if ($this->output != "")
		{
			$nesting_point = 'id="' . $field_id . '">';
			$this->output = str_replace($nesting_point, $nesting_point . $content, $this->output);
		}
	}

}
