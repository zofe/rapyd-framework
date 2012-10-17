<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');



class date_field extends field_field {

  public $type = "date";
  public $css_class = "input";
  public $clause = "where";

	// --------------------------------------------------------------------

  public function get_new_value()
  {
    parent::get_new_value();
    if (isset($_POST[$this->name]))
    {
      $this->new_value = date_helper::human2iso($this->new_value);
    }
  }

	// --------------------------------------------------------------------

  function build()
  {
		$output = "";
    html_helper::css('jquery/smoothness.datepick.css');
    html_helper::js('jquery/jquery.min.js');
    html_helper::js('jquery/jquery.datepick.pack.js');
	if (!in_array(rpd::get_lang('locale'),array('en_US','en_GB')))
		html_helper::js('jquery/jquery.datepick.'.rpd::get_lang('locale').'.js');

    if(!isset($this->size))
    {
      $this->size = 25;
    }

    if (parent::build() === false) return;

    switch ($this->status)
    {

      case "show":
        if (!isset($this->value))
        {
          $value = $this->layout['null_label'];
        } elseif ($this->value == ""){
          $value = "";
        } else {
          $value = date_helper::iso2human($this->value);
        }
        $output = $value;
        break;

      case "create":
      case "modify":

        $value = "";
        if ($this->value != ""){
           if ($this->is_refill){
             $value = $this->value;
           } else {
             $value = date_helper::iso2human($this->value);
           }
        }
        $this->attributes['type'] = 'input';
        $output  = form_helper::input($this->attributes, $value);
        $output .= html_helper::script('
			$(function() {
				$("#'.$this->name.'").datepick();
			});');

        break;

      case "disabled":
        //versione encoded
        $output = date_helper::iso2human($this->value);
        break;

      case "hidden":
        $output =  form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
