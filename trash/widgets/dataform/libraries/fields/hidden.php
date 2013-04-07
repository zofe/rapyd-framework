<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class hidden_field extends field_field {

  public $type = "auto";

  function build()
  {
    $this->output = "";
  }

}