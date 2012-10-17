<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class password_field extends field_field {

  public $type = "password";
  public $css_class = "input";

  public function build()
  {
    $output = "";
    if(!isset($this->size))
    {
      $this->size = 35;
    }
    unset($this->attributes['type']);
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":
        if ( (!isset($this->value)) )
        {
          $output = $this->layout['null_label'];
        } elseif ($this->value == ""){
          $output = "";
        } else {
          $output = nl2br(htmlspecialchars($this->value));
        }
        break;

      case "create":
      case "modify":
        $output = form_helper::input($this->attributes, $this->value);
        break;

      case "hidden":
        $output = form_helper::hidden($this->attributes, $this->value);
        break;

      default:;
    }
    $this->output = "\n".$this->before_output."\n".$output."\n". $this->extra_output."\n";
  }

}
