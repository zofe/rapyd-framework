<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class dropdown_field extends field_field {

  public $type = "select";
  public $description = "";
  public $clause = "where";
  public $css_class = "select";

	// --------------------------------------------------------------------

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

	// --------------------------------------------------------------------

  function build()
  {
   $output = "";
    if(!isset($this->style)&& !isset($this->attributes['style']))
    {
      $this->style = "width:290px;";
    }
    unset($this->attributes['type'],$this->attributes['size']);
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
         $output = $this->before_output.form_helper::dropdown($this->attributes, $this->options, $this->value). $this->extra_output;
        break;

      case "hidden":
        $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
