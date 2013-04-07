<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class input_field extends field_field {

  public $type = "text";
  public $css_class = "";

  public function build()
  {
    $output = "";
    if(!isset($this->size))
    {
      $this->size = 35;
    }
    unset($this->attributes['type']);
    if (parent::build() === false) return;


    //http://digitalbush.com/projects/masked-input-plugin
    if (isset($this->mask))
    {
      html_helper::js('jquery/jquery.min.js');
      html_helper::js('jquery/jquery.maskedinput.js');
    }

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
	$output .= "\n". $this->extra_output;
        if (isset($this->mask))
        {
          $output .= html_helper::script('
                $(function(){
                  $("#'.$this->name.'").mask("'.$this->mask.'");
                });');
        }
        break;

      case "hidden":
        $output = form_helper::hidden($this->attributes, $this->value);
        break;

      default:;
    }
    $this->output = "\n".$this->before_output."\n".$output."\n";
  }

}
