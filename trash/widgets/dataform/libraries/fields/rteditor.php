<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rteditor_field extends field_field {

  public $type = "rteditor";
  public $css_class = "rteditor";

  function get_new_value()
  {
    parent::get_new_value();
    if (isset($this->new_value))
    {
      if (substr($this->new_value, strlen($this->new_value)-4, 4) == '<br>')
        $this->new_value =  substr($this->new_value, 0, strlen($this->new_value)-4);
    }
  }

  function build()
  {
    $output = "";
    html_helper::js('assets/jquery/jquery.min.js');
    html_helper::js('assets/jqueryrte/jquery.rte.js');
    html_helper::js('assets/jqueryrte/jquery.rte.tb2.js');
    html_helper::css('assets/jqueryrte/jquery.rte.css');


    if(!isset($this->cols))
    {
      $this->cols = 45;
    }
    if(!isset($this->rows)){
      $this->rows = 15;
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
        }
        elseif ($this->value == "")
        {
          $output = "";
        }
        else
        {
          //$output = '<div class="textarea_disabled">'.nl2br(htmlspecialchars($this->value)).'</div>';
          $output = '<div class="textarea_disabled">'.nl2br(strip_tags($this->value)).'</div>';
        }
        break;

      case "create":
      case "modify":


        $output  = form_helper::textarea($this->attributes, $this->value);
	$output .= $this->extra_output."\n";
        $output .= html_helper::script("

				var inst_".$this->name."
                                $(document).ready(function() {
                                        inst_".$this->name." = $('textarea#".$this->name."').rte({
                                            	css: ['".rpd::config('core_assets_uri')."jquery/rte.css'],
                                                controls_rte: rte_toolbar,
                                                controls_html: html_toolbar
                                        });
                                });");

        break;

      case "hidden":

        $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = "\n".$this->before_output.$output."\n";
  }

}
