<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');

class upload_field extends field_field {

	public $type = "upload";
	public $css_class = "input";

	public $delete_file = true;
	public $www_path;
	public $upload_path;
	public $upload_root;
	public $preview;
	public $popup_params;

	public $upload_error;
	public $allowed_types = array();
	public $max_size;
	public $crop_to = array();
	

	// --------------------------------------------------------------------

	protected function server_path($docroot) {
		$base = ($docroot != "") ? $docroot : $_SERVER["DOCUMENT_ROOT"];
		return $base . $this->upload_path;
	}

	// --------------------------------------------------------------------

	public function set_upload_path($path) {
		$this->upload_path= $path;
		return $this;
	}
	
	public function set_www_path($path) {
		$this->www_path= $path;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_upload_root($root) {
		$this->upload_root= $root;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_allowed_types($types) {
		if (is_array($types)){
			$this->allowed_types = $types;
		} elseif (is_string($types) AND strpos($types, '|')) {
			$this->allowed_types = explode('|', $types);
		}
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_max_size($size) {
		$this->max_size = $size;
		return $this;
	}

	// --------------------------------------------------------------------

	public function set_crop($width, $height) {
		$this->crop_to = array($width, $height);
		return $this;
	}

	
	// --------------------------------------------------------------------

	protected function draw_link() {
		if (isset($this->preview)) {
			return $this->draw_preview_link();
		} elseif (isset($this->thumb)) {
			$this->preview = $this->thumb;
			return $this->draw_preview_link();
		}
		return $this->draw_upload_link();
	}

	// --------------------------------------------------------------------

	protected function draw_upload_link() {
		if ($this->www_path == "") {
			$this->www_path = $this->upload_path;
		} elseif($this->www_path == "")  {
			$this->www_path = rpd::url('public/uploads/');
		}
		$action = "javascript:window.open('" . $this->www_path . $this->value . "','" . $this->name . "','" . $this->popup_params . ",');";
		return '<a onclick="' . $action . '" href="javascript:void(0);">' . $this->value . '</a>';
	}



	// --------------------------------------------------------------------

	protected function exec_upload() {
		$this->get_value();

		if ($this->max_size!=''){
			if (!upload_helper::size($_FILES[$this->name . "_user_file"], $this->max_size)){
				$this->save_error = $this->label . ": Max Size is ".$this->max_size;
				return false;
			}
		}
		if (count($this->allowed_types)){
			if (!upload_helper::type($_FILES[$this->name . "_user_file"], $this->allowed_types)){
				$this->save_error = $this->label . ": Allowed Types are ".implode(',',$this->allowed_types);
				return false;
			}
		}


		$file = upload_helper::save($this->name . "_user_file",  $this->upload_path);
		if ($file){
			if (count($this->crop_to)>1)
			{
				image_helper::crop($this->upload_path.$file, $this->upload_path.$file, $this->crop_to[0], $this->crop_to[1]);
			}
			return $file;
		}
		
		$this->save_error = $this->label . ": Upload Error";
		return false;

	}

	// --------------------------------------------------------------------

	protected function exec_unlink() {
		$this->get_value();
		if ($this->delete_file) {
			$filename = $this->value;
			@unlink($this->upload_path . $filename);
		}
	}

	// --------------------------------------------------------------------

	public function auto_update($store = false) {
		$this->get_value();
		//required
		if (($_POST[$this->name] == "") && ($_FILES[$this->name . "_user_file"]["name"] == "") || ((isset($_POST[$this->name . "_checkbox"])) && ($_POST[$this->name . "_checkbox"] == "True"))) {
			if (isset($this->rule) && ($this->rule == "required")) {
				$this->save_error = sprintf("Il campo \"%s\" deve contentere un valore.", $this->label);
				return false;
			}
		}
		if ((($this->action == "update") || ($this->action == "insert"))) {
			if ($_FILES[$this->name . "_user_file"]["name"] == "") {
				if (isset($_POST[$this->name . "_checkbox"])) {
					if ($_POST[$this->name . "_checkbox"] == "True") {
						$this->exec_unlink();
						$this->new_value = null;
					}
				} else {
					$this->new_value = $this->value;
				}
			} else {
				if ($filename = $this->exec_upload()) {
					$this->new_value = $filename;
				} else {
					return false;
				}
			}
			if (isset($this->model) AND is_object($this->model)) {
				$this->model->set($this->name, $this->new_value);
				if ($store) $this->model->save();
			}
		}
		return true;
	}

	// --------------------------------------------------------------------

	public function build() {
		$output = "";
		if (!isset($this->style) && !isset($this->attributes['style'])) {
			$this->style = "width:290px;";
		}
		/* i know.. but i'm little bit drunk today..*/
		if (isset($this->attributes['style'])) {
			$this->style = $this->attributes['style'];
		}
		if (!isset($this->size)) {
			$this->size = null;
		}
		unset($this->attributes['type'], $this->attributes['size']);
		if (parent::build() === false) return;
		switch ($this->status) {
			case "show":
			case "disabled":
				if ((!isset($this->value)) || ($this->value == "")) {
					$output = $this->layout['null_label'];
				} else {
					$output = $this->draw_link();
				}
			break;
			case "create":
			case "modify":
				$output = $this->before_output.'<div style="">';
				if (!(!isset($this->value) || ($this->value == ""))) {
					$output.= $this->draw_link();
					$output.= "&nbsp;-&nbsp;";
					$attributes = array('name' => $this->name . "_checkbox", 'id' => $this->name . "_checkbox", 'value' => 'True', 'checked' => false, 'style' => "vertical-align:middle;");
					$output.= form_helper::checkbox($attributes) . " rimuovi<br />\n";
				}
				//$output.= "cerca<br />\n";
				$output.= form_helper::hidden($this->name, $this->value);
				$attributes = array('name' => $this->name . "_user_file", 'id' => $this->name . "_user_file", 'size' => $this->size, 'onclick' => $this->onclick, 'onchange' => $this->onchange, 'class' => $this->css_class, 'style' => $this->style);
				$output.= form_helper::upload($attributes);
				$output.= "</div>" . $this->extra_output;
			break;
			case "hidden":
				$output = form_helper::hidden($this->name, $this->value);
			break;
			default:
		}
		$this->output = $output;
	}

}
