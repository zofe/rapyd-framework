<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class textarea_field extends field_field {

  public $type = "textarea";
  public $css_class = "textarea";


  function build()
  {
    $output = "";
    if(!isset($this->cols))
    {
      $this->cols = 45;
    }
    if(!isset($this->rows)){
      $this->rows = 15;
    }
    unset($this->attributes['type'],$this->attributes['size']);
    if (parent::build() === false) return;

    if (isset($this->max_chars))
    {
      html_helper::js('jquery/jquery.min.js');

			$output .= html_helper::script('

				function limit_chars_'.$this->name.'()
				{
				  var limit = '.$this->max_chars.';
					var text = $("#'.$this->name.'").val();
					var textlength = text.length;


					if(textlength > limit)
					{
					 $("#'.$this->name.'_info").html(" non puoi superare i "+limit+" caratteri!");
					 $("#'.$this->name.'").val(text.substr(0,limit));
					 return false;
					}
					else
					{
					 $("#'.$this->name.'_info").html("hai "+ (limit - textlength) +" caratteri rimanenti");
					 return true;
					}


				}

				$("#'.$this->name.'").keyup(function(){
					limit_chars_'.$this->name.'();
				});

			');

      $this->attributes['onkeyup'] = "limit_chars_".$this->name."()";
			$this->extra_output .= '<div id="'.$this->name.'_info">'.$this->max_chars.' caratteri rimanenti</div>';
    }


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
          $output = '<div class="textarea_disabled">'.nl2br(htmlspecialchars($this->value)).'</div>';
        }
        break;

      case "create":
      case "modify":
          $output = $this->before_output.form_helper::textarea($this->attributes, $this->value) .$this->extra_output;
        break;

      case "hidden":

        $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
?>
