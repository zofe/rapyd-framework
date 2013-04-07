<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class iframe_field extends field_field {

  public $type = "iframe";
  public $css_class = null;

  public $iframe = null;
  public $iframe_url = null;

  public $height = "200";
  public $scrolling = "auto";
  public $frameborder = "0";
  public $db_name = null;
  public $url;


  function get_value()
  {
    if (isset($this->model) AND is_object($this->model))
    {
      $this->url = parent::replace_pattern($this->url,$this->model->get_all());
    }
    $this->iframe = '<IFRAME src="'. $this->url .'" width="100%" height="'.$this->height.'" scrolling="'.$this->scrolling.'" frameborder="'.$this->frameborder.'" id="'.$this->name.'">iframe not supported</IFRAME>';
    $this->value =  $this->iframe;
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

      default:
    }
    $this->output = "\n".$output."\n";

  }

}
