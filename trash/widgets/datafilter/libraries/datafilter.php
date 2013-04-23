<?php

if (!defined('CORE_PATH'))
	exit('No direct script access allowed');

/**
 * Datafilter library
 * 
 * @package    Core
 * @author     Felice Ostuni
 * @copyright  (c) 2011 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class datafilter_library extends dataform_library
{

	/**
	 * datafilter basically work with active record db class to build a where clause
	 * 
	 * @todo check if source can be also a model, then change code otherwise it will not work
	 * @param array $config 
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->connect();
		if (isset($this->source))
		{
			$this->db->select('*');
			$this->db->from($this->source);
		}
		$this->status = 'create';

	}

	/**
	 * detect current dataform "action" checking current url
	 */
	protected function sniff_action()
	{

                $url = ($this->url != '') ? $this->url : url_helper::get_url();
                
                
                
		///// search /////
		if (url_helper::value('search'))
		{

			if(url_helper::value("search")==='1')
			{
				$url = url_helper::append("search", url_helper::array_to_url_title($_POST));
			}

			$this->action = "search";
			// persistence
			sess_helper::save_persistence($url);

			if(url_helper::value("search")==='1')
			{
				rpd::redirect(url_helper::uri($url));
			}

		}
		///// reset /////
		elseif (url_helper::value("reset"))
		{
			$this->action = "reset";
			// persistence cleanup
			sess_helper::clear_persistence($url);
		}
		///// show /////
		else
		{
			$page = sess_helper::get_persistence($url);
			if (count($page))
			{
				$this->action = "search";
			}
			// persistence
			sess_helper::save_persistence($url);
		}
	}

        

        
	/**
	 * internal, process an action: (reset or search)
	 * check each field value and prepare a WHERE  clause using active record 
	 * 
	 * @todo fix for checkboxgroup as nico suggested http://www.rapyd.com/forum/post/21/#p21
	 * @return type 
	 */
	public function process()
	{
		$result = parent::process();
		switch ($this->action)
		{
			case "search":
				// prepare the WHERE clause
				foreach ($this->fields as $field)
				{
					if ($field->value != "")
					{
						if (strpos($field->name, "_copy") > 0)
						{
							$name = substr($field->db_name, 0, strpos($field->db_name, "_copy"));
						} else
						{
							$name = $field->db_name;
						}
						$field->get_value();
						$field->get_new_value();
						$value = $field->new_value;
						switch ($field->clause)
						{
							case "like":
								$this->db->like($name, $value);
								break;
							case "orlike":
								$this->db->orlike($name, $value);
								break;
							case "where":
								$this->db->where($name . " " . $field->operator, $value);
								break;
							case "orwhere":
								$this->db->orwhere($name . " " . $field->operator, $value);
								break;
                            case "whereor":
								$this->db->whereor($name . " " . $field->operator, $value, $field->valueor);
							break; 
							case "match":
                                //die('MATCH ('.$name.') AGAINST ('.$this->db->escape($value).')');
                                $this->db->where('MATCH ('.$name.') AGAINST ('.$this->db->escape($value).')');
							break;
                        
						}
					}
				}
                                
				$this->build_buttons();
				return $this->build_form();
				break;
			case "reset":
				//pulire sessioni
				$this->build_buttons();
				return $this->build_form();
				break;
			default:
				$this->build_buttons();
				return $this->build_form();
				break;
		}
                
                
	}

	/**
	 * internal, add a submit button
	 * 
	 * @param array $config 
	 */
	function search_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.search');
		$this->submit("btn_submit", $caption, "BL");
	}

	/**
	 * internal, add a reset button
	 * 
	 * @param array $config 
	 */
	function reset_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.reset');
		$action = "javascript:window.location='" . $this->reset_url . "'";
		$this->button("btn_reset", $caption, $action, "BL");
	}

	/**
	 * main method it detect status, process form and build output
	 */
	function build()
	{
                $url = ($this->url != '') ? $this->url : url_helper::get_url();
		$url = url_helper::remove_all(null, $url);
		$this->reset_url = url_helper::append('reset', 1, $url);
		$this->process_url = url_helper::append('search', 1, $url);
		$this->reset_url = $this->reset_url . $this->hash;
		$this->process_url = $this->process_url . $this->hash;
                
                $this->sniff_action();

		//sniff and build fields
		$this->sniff_fields();
		//build fields
		$this->build_fields();
		$this->output = $this->process();
                
                //var_dump($_SESSION); 
	}

}
