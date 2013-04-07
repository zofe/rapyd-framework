<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class captcha_field extends field_field {

  public $type = "captcha";
  public $rule = "required|captcha";
  public $css_class = "input";

  function build()
  {
	$output = '';

    if(!isset($this->size)){
      $this->size = 10;
    }
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":

         $output = "";
        break;

      case "create":
      case "modify":

        $value = "";
        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => "text",
          'value'       => $value,
          'maxlength'   => $this->maxlength,
          'size'        => $this->size,
          'onclick'     => $this->onclick,
          'onchange'    => $this->onchange,
          'class'       => $this->css_class,
          'style'       => $this->style
          );
        $output  = html_helper::image('captchaimg.php?'.time(),array('style'=>'vertical-align:middle'));
        $output .= $this->before_output.form_helper::input($attributes) . $this->extra_output;
        break;

      case "hidden":

        $attributes = array(
          'name'        => $this->name,
          'id'          => $this->name,
          'type'        => "hidden",
          'value'       => $this->value);
        $output = form_helper::input($attributes);
        break;

      default:
    }
    $this->output = "\n".$output."\n";
  }

}