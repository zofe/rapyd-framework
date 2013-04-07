<?php



/**
 * Dataedit library
 * 
 * @todo optimizations.. see yii/kohana widgets
 * @todo a dataedit basically is a sub-application, i can build a controller and use "rpd::run(..)"? 
 * @package    Core
 * @author     Felice Ostuni
 * @copyright  (c) 2011 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class dataedit_library extends dataform_library
{

	//flow
	protected $postprocess_url = "";
	protected $undo_url = "";
	public $back_url = "";
	public $back_save = false;
	public $back_delete = true;
	public $back_cancel = false;
	public $buttons = array();
	public $back_cancel_save = false;
	public $back_cancel_delete = false;

	/**
	 * internal, add the modify button
	 * 
	 * @param array $config 
	 */
	protected function modify_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.modify');
		if ($this->status_is("show") && url_helper::value('show' . $this->cid))
		{
			$modify_url = url_helper::replace('show' . $this->cid, 'modify' . $this->cid);
			$action = "javascript:window.location.href='" . $modify_url . "'";
			$this->button("btn_modify", $caption, $action, "TR");
		}
	}

	/**
	 * internal, add the delete button
	 * 
	 * @param array $config 
	 */
	protected function delete_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.delete');
		if ($this->status_is("show") && url_helper::value('show' . $this->cid))
		{
			$delete_url = url_helper::replace('show' . $this->cid, 'delete' . $this->cid);
			$action = "javascript:window.location.href='" . $delete_url . "'";
			$this->button("btn_delete", $caption, $action, "TR");
		} elseif ($this->status_is("delete"))
		{
			$action = "javascript:window.location.href='" . $this->process_url . "'";
			$this->button("btn_delete", $caption, $action, "BL");
		}
	}

	/**
	 * internal, add a save button
	 * 
	 * @todo check if it's needed, dataform already has this method
	 * @param array $config 
	 */
	public function save_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.save');
		if ($this->status_is(array("create", "modify")))
		{
			$this->submit("btn_submit", $caption, "BL");
		}
	}

	/**
	 * internal, add an undo-modify button
	 * 
	 * @param array $config 
	 */
	protected function undo_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.undo');
		if ($this->status_is("create"))
		{
			$action = "javascript:window.location.href='{$this->back_url}'";
			$this->button("btn_undo", $caption, $action, "TR");
		} elseif ($this->status_is("modify"))
		{
			if (($this->back_cancel_save === FALSE) || ($this->back_cancel === FALSE))
			{
				//is modify
				if (url_helper::value('modify' . $this->cid))
				{
					$undo_url = url_helper::replace('modify' . $this->cid, 'show' . $this->cid);
				}
				//is modify on error
				elseif (url_helper::value('update' . $this->cid))
				{
					$undo_url = url_helper::replace('update' . $this->cid, 'show' . $this->cid);
				}
				$action = "javascript:window.location.href='" . $undo_url . "'";
			} else
			{
				$action = "javascript:window.location.href='{$this->back_url}'";
			}
			$this->button("btn_undo", $caption, $action, "TR");
		} elseif ($this->status_is("delete"))
		{
			if (($this->back_cancel_delete === FALSE) || ($this->back_cancel === FALSE))
			{
				$action = "javascript:window.location.href='{$this->undo_url}'";
			} else
			{
				$action = "javascript:window.location.href='{$this->back_url}'";
			}
			$this->button("btn_undo", $caption, $action, "TR");
		}
	}

	/**
	 * internal, add a back button
	 * 
	 * @param array $config 
	 */
	protected function back_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.back');
		if ($this->status_is(array("show", "unknow_record")) || $this->action_is("delete"))
		{
			$action = "javascript:window.location.href='{$this->back_url}'";
			$this->button("btn_back", $caption, $action, "BL");
		}
	}

	/**
	 * add a back on error button
	 * 
	 * @param array $config 
	 */
	protected function backerror_button($config = null)
	{
		$caption = (isset($config['caption'])) ? $config['caption'] : rpd::lang('btn.back_error');
		if ($this->action_is("do_delete") && $this->status_is("error"))
		{
			$action = "javascript:window.history.back()";
			$this->button("btn_backerror", $caption, $action, "TR");
		}
	}

	/**
	 * detect current dataedit "status" checking current url
	 * 
	 * @todo is the right choise use only url to detect status? 
	 * @todo make a better centralization of status/action inside "component" class
	 */
	protected function sniff_status()
	{
		$this->set_status("idle");
		///// show /////
		if (url_helper::value('show' . $this->cid))
		{
			$this->set_status("show");
			$this->process_url = "";
			if (!$this->model->load(url_helper::value('show' . $this->cid)))
			{
				$this->set_status("unknow_record");
			}
			///// modify /////
		} elseif (url_helper::value('modify' . $this->cid))
		{
			$this->set_status("modify");
			$this->process_url = url_helper::replace('modify' . $this->cid, 'update' . $this->cid);
			if (!$this->model->load(url_helper::value('modify' . $this->cid)))
			{
				$this->set_status("unknow_record");
			}
			///// create /////
		} elseif (url_helper::value('create' . $this->cid))
		{
			$this->set_status("create");
			$this->process_url = url_helper::replace('create' . $this->cid, 'insert' . $this->cid);
			///// delete /////
		} elseif (url_helper::value('delete' . $this->cid))
		{
			$this->set_status("delete");
			$this->process_url = url_helper::replace('delete' . $this->cid, 'do_delete' . $this->cid);
			$this->undo_url = url_helper::replace('delete' . $this->cid, 'show' . $this->cid);
			if (!$this->model->load(url_helper::value('delete' . $this->cid)))
			{
				$this->set_status("unknow_record");
			}
		} elseif (url_helper::value('inset' . $this->cid . '|update' . $this->cid . '|do_delete' . $this->cid))
		{
			//status is idle.. action is executed
		} else
		{
			$this->set_status("unknow_record");
		}
	}

	/**
	 * detect current dataedit "action" checking current url
	 */
	protected function sniff_action()
	{
		///// insert /////
		if (url_helper::value('insert' . $this->cid))
		{
			$this->set_action("insert");
			$this->postprocess_url = url_helper::replace('insert' . $this->cid, 'show' . $this->cid);
			///// update /////
		} elseif (url_helper::value('update' . $this->cid))
		{
			$this->set_action("update");
			$this->postprocess_url = url_helper::remove('update' . $this->cid);
			if (!$this->model->load(url_helper::value('update' . $this->cid)))
			{
				$this->set_status("unknow_record");
			}
			///// delete /////
		} elseif (url_helper::value("do_delete" . $this->cid))
		{
			$this->set_action("delete");
			if (!$this->model->load(url_helper::value("do_delete" . $this->cid)))
			{
				$this->set_status("unknow_record");
			}
		}
	}

	/**
	 * internal, run form validation and return boolean result
	 * 
	 * @return bool
	 */
	protected function is_valid()
	{
		$result = parent::is_valid();
		if ($this->action_is("update") || $this->action_is("insert"))
		{
			$pk_check = array();
			$pk_error = "";
			$hiddens = array();
			//pk fields mode can setted to "autohide" or "readonly" (so pk integrity violation check isn't needed)
			foreach ($this->fields as $field)
			{
				$field->get_value();
				if (!$field->apply_rules)
				{
					$hiddens[$field->db_name] = $field->value;
				}
			}
			//We build a pk array from the form value that is submit if its a writing action (update & insert)
			foreach ($this->model->pk as $keyfield => $keyvalue)
			{
				if (isset($this->validation->$keyfield))
				{
					$pk_check[$keyfield] = $this->validation->$keyfield;
				} elseif (array_key_exists($keyfield, $hiddens))
				{
					$pk_check[$keyfield] = $hiddens[$keyfield];
				}
			}
			if (sizeof($pk_check) != count($this->model->pk))
			{
				//if (sizeof($this->model->pk)==1 && sizeof($pk_check)==0) return $result;
				if (sizeof($pk_check) == 0)
					return $result;
			}
			if ($result && !$this->model->are_unique($pk_check))
			{
				$result = false;
				$pk_error.= rpd::lang('de.err_dup_pk');
				$this->error_string.= $pk_error;
			}
		}
		return $result;
	}

	/**
	 * internal, process an action: try to save posted data on model, and display result or an error 
	 * 
	 * @todo redirect after profess are all in javascript (window.location), this is really bad, but there is some reason i don't remember
	 * @todo move language specific messages to i18n files
	 * @return string
	 */
	public function process()
	{
		$result = parent::process();
		switch ($this->action)
		{
			case "update":
				if ($this->on("error"))
				{
					$this->set_status("modify");
					$this->process_url = url_helper::get_url();
					$this->build_buttons();
					$this->build_fields(); //rebuild fields to update new status (strictly needed?)
					return $this->build_form();
				}
				if ($this->on("success"))
				{
					$qs = (count($this->model->pk) < 2) ? current($this->model->pk) : $this->model->pk;
					$this->postprocess_url = url_helper::append('show' . $this->cid, $qs, $this->postprocess_url);
					if ($this->back_save)
					{
						return html_helper::script("javascript:window.location.href='" . $this->back_url . "'");
						die();
					} else
					{
						return html_helper::script("javascript:window.location.href='" . $this->postprocess_url . "'");
						die();
					}
				}
				break;
			case "insert":
				if ($this->on("error"))
				{
					$this->set_status("create");
					$this->process_url = url_helper::get_url();
					$this->build_buttons();
					$this->build_fields(); //rebuild fields to update new status (strictly needed?)
					return $this->build_form();
				}
				if ($this->on("success"))
				{
					$qs = (count($this->model->pk) < 2) ? reset($this->model->pk) : $this->model->pk;
					$this->postprocess_url = url_helper::append('show' . $this->cid, $qs, $this->postprocess_url);
					if ($this->back_save)
					{
						return html_helper::script("javascript:window.location.href='" . $this->back_url . "'");
						die();
					} else
					{
						return html_helper::script("javascript:window.location.href='" . $this->postprocess_url . "'");
						die();
					}
				}
				break;
			case "delete":
				if ($this->on("error"))
				{
					$this->build_buttons();
					return $this->build_message($this->error_string);
				}
				if ($this->on("success"))
				{
					$this->build_buttons();
					if ($this->back_delete)
					{
						return html_helper::script("javascript:window.location.href='" . $this->back_url . "'");
						die();
					} else
					{
						return $this->build_message(rpd::lang('de.deleted'));
					}
				}
				break;
		}
		switch ($this->status)
		{
			case "show":
			case "modify":
			case "create":
				$this->build_buttons();
				return $this->build_form();
				break;
			case "delete":
				$this->build_buttons();
				return $this->build_message(rpd::lang('de.confirm_delete'));
				break;
			case "unknow_record":
				$this->build_buttons();
				return $this->build_message(rpd::lang('de.err_read'));
				break;
		}
	}

	/**
	 * internal, return a message
	 * 
	 * @param type $message
	 * @return string  
	 */
	protected function build_message($message)
	{
		html_helper::css('dataform.css');
		$data = get_object_vars($this);
		$data['container'] = $this->button_containers();
		$form_type = 'open';
		// Set the form open and close
		$data['form_begin'] = '';
		$data['form_end'] = '';
		form_helper::close();
		$data["message"] = $message;
		return rpd::view('dataform', $data);
	}

	/**
	 * main method it detect status, exec action and build output
	 * 
	 * @todo back_url seems wrong (index.php should be conditional)
	 * @todo many string should be moved inside i18n language files
	 * @param type $method 
	 */
	public function build()
	{
		//detect form status (output)
		if (isset($this->model))
		{
			$this->status = ($this->model->loaded) ? "modify" : "create";
		} else
		{
			$this->show_error(rpd::lang('de.err_no_model'));
		}
		if (($this->back_url == "") && isset($this->buttons["back"]))
		{
			$this->show_error(rpd::lang('de.err_no_backurl'));
		}
		$this->sniff_status();
		//build fields
		$this->build_fields();
		//sniff and perform action
		$this->sniff_action();

		//build back_url with persistence 
        $back_url = $this->back_url;
		$back_url = rpd::url(rpd::uri($this->back_url));
		
		if (isset($_SESSION['rapyd'][$back_url])) {
			$persistence = $_SESSION['rapyd'][$back_url];
			$this->back_url = $persistence["back_url"];
		}
		//process
		$this->output = $this->process();
	}

}
