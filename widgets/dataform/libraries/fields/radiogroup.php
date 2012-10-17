<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');

class radiogroup_field extends field_field {

  public $type = "radio";
  public $size = null;
  public $description = "";
  public $separator = "&nbsp;&nbsp;";
  public $clause = "where";

  function get_value()
  {
    parent::get_value();
    foreach ($this->options as $value=>$description)
    {
      if ($this->value == $value)
      {
        $this->description = $description;
      }
    }
  }

  function build()
  {
    $output = "";
    if(!isset($this->style))
    {
      $this->style = "margin:0 2px 0 0; vertical-align: middle";
    }
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":
        if (!isset($this->value))
        {
          $output = $this->layout['null_label'];
        } else {
          $output = $this->description;
        }
        break;

      case "create":
      case "modify":

        foreach ($this->options as $val => $label )
        {
          $this->checked = (!is_null($this->value) AND ($this->value == $val));
          $output .= form_helper::radio($this->attributes, $val ,$this->checked).$label.$this->separator;
        }
        $output .=  $this->extra_output();
        break;

      case "hidden":
        $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }


}
