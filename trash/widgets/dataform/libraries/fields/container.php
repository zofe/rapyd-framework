<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class container_field extends field_field {

  public $type = "container";

  function get_value()
  {
    parent::get_value();

    if (isset($this->pattern))
    {
      $this->value = $this->replace_pattern($this->pattern,$this->model->get_all());
      $this->value = $this->replace_functions($this->value);
    }
  }

  function build()
  {
    $output = "";
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "show":
      case "create":
      case "modify":

        $output = $this->value;
        break;

      case "hidden":

        $output = "";

        break;

      default:;
    }
    $this->output = "\n".$output."\n";
  }

}
