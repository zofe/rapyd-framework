<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class checkbox_field extends field_field {

  public $type = "checkbox";
  public $size = null;
  public $checked = false;
  public $css_class = "checkbox";
  public $checked_value = 1;
  public $unchecked_value = 0;
  
  public $checked_output = 'Yes';
  public $unchecked_output = 'No';
  //per il css  "vertical-align:middle";

  function get_value()
  {
    parent::get_value();

    /*if (!isset($_POST[$this->name]))
    {
      $this->value = $this->unchecked_value;
    }*/

    $this->checked = (bool)($this->value == $this->checked_value);
  }

  function get_new_value()
  {
    parent::get_new_value();
    if (!isset($_POST[$this->name]))
    {
     $this->new_value = $this->unchecked_value;
    }
    $this->checked = (bool)($this->value == $this->checked_value);
  }

  function build()
  {
    $output = "";
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":
        if (!isset($this->value)){
          $output = $this->layout['null_label'];
        } else {
          $output =  ($this->checked) ? $this->checked_output : $this->unchecked_output;
        }
        break;

      case "create":
      case "modify":
            $output = $this->before_output.form_helper::checkbox($this->attributes, $this->checked_value , $this->checked).$this->extra_output;
        break;

      case "hidden":
            $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
