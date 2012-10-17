<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class html_field extends field_field {

  public $type = "html";
  public $size = null;
  public $css_class = "textarea_html";

  function get_new_value()
  {
    parent::get_new_value();
    if (isset($_POST[$this->name]))
    {
      $this->new_value = $this->new_value;////htmlspecialchars();
    }
  }

  function build()
  {
    $output = "";

    if(!isset($this->cols))
    {
      $this->cols = 60;
    }
    if(!isset($this->rows)){
      $this->rows = 15;
    }
    unset($this->attributes['type']);
    if (parent::build() === false) return;

    switch ($this->status)
    {
      case "disabled":
      case "show":
        if (!isset($this->value)) {
          $output = $this->layout['null_label'];
        } elseif ($this->value == ""){
          $output = "";
        } else {
          $output = '<div class="textarea_html_disabled"><pre>'.(html::specialchars($this->value)).'</pre></div>';
        }
        break;

      case "create":
      case "modify":
        $output = $this->before_output.form_helper::textarea($this->attributes, $this->value) .$this->extra_output;
        break;

      case "hidden":

        $output = "";//form_hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
?>
