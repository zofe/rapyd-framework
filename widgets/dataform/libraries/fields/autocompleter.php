<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');

class autocompleter_field extends field_field {

  public $type = "autocompleter";
  public $css_class = "autocompleter";
  public $is_multiple = false;

  public $ajax_source;
  public $ajax_rsource;
  
  public $record_id;
  public $record_label;
  public $hidden_field_id;
  public $oncomplete;
  public $must_match = false;
  public $auto_fill = false;
  public $parent_id = '';


  function get_value()
  {
    parent::get_value();

    if (!isset($_POST[$this->name]))
    {
        if (isset($this->ajax_rsource, $this->hidden_field_id))
        {
            if (isset($this->model) AND is_object($this->model) AND $this->model->loaded)
            {
                $this->ajax_rsource = parent::replace_pattern($this->ajax_rsource,$this->model->get_all());
                $this->value = file_get_contents($this->ajax_rsource);
            }


        }
    }

  }

/*  function get_new_value()
  {
    parent::get_new_value();
    if (!isset($_POST[$this->name]))
    {
     $this->new_value = $this->unchecked_value;
    }
  }
 */

  public function build()
  {
    $output = "";

    html_helper::js('jquery/jquery.min.js');
    html_helper::js('jquery/jquery.autocomplete.min.js');
    html_helper::css('jquery/autocomplete.css');

    //textarea autocompleter
    if ($this->is_multiple)
    {
        if(!isset($this->cols))
        {
          $this->cols = 45;
        }
        if(!isset($this->rows))
        {
          $this->rows = 15;
        }

    //normal input autocompleter
    } else {

        if(!isset($this->size))
        {
          $this->size = 35;
        }
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
          if ($this->is_multiple)
                $output = '<div class="textarea_disabled">'.$output.'</div>';
        }
        break;

      case "create":
      case "modify":

		    $output  = $this->before_output."\n";
            $output .= (!$this->is_multiple) ? form_helper::input($this->attributes, $this->value) :
                                             form_helper::textarea($this->attributes, $this->value);
		    $output .= $this->extra_output."\n";
			
            $mustmatch = ($this->must_match) ? 'true' : 'false';
            $autofill = ($this->auto_fill) ? 'true' : 'false';
            $extraParams = ($this->parent_id!='') ? "extraParams : { 'parent_id': function() { return $('#".$this->parent_id."').val(); } },"  : "";

        $script = <<<acp

                    jQuery("#{$this->name}").autocomplete("{$this->ajax_source}", {
                            limit: 10,
                            minChars: 2,
                            {$extraParams}
                            mustMatch: {$mustmatch},
                            autoFill: {$autofill},
                            dataType: "json",
                            parse: function(data) {

acp;
        if($this->hidden_field_id != "")
        {
            $script .="\tjQuery('#{$this->hidden_field_id}').val('');\n";
        }

        $script .= <<<acp
                                    return jQuery.map(data, function(row) {
                                            return {
                                                    data: row,
                                                    value: row.{$this->record_id},
                                                    result: row.{$this->record_label}
                                            }
                                    });
                            },
                            formatItem: function(item) {
                                    return item.{$this->record_label};
                            }
                    }).result(function(e, item) {
                        if (item)
                        {
                          jQuery("#{$this->name}").val(item.{$this->record_label});

acp;


            if($this->hidden_field_id != "")
            {
                $script .= "\tjQuery('#{$this->hidden_field_id}').val(item.{$this->record_id});\n";
            }

            if($this->oncomplete != "")
            {
                $script .= "\t{$this->oncomplete}(item); \n";
            }


            $script .= "\n } else { \n";
            $script .= "\tjQuery('#{$this->name}').val('');\n";
            if($this->hidden_field_id != "")
            {
                 $script .= "\tjQuery('#{$this->hidden_field_id}').val('');\n";
            }
            $script .= <<<acp
                        }
                });
acp;

        $output .= html_helper::script($script);







        break;

      case "hidden":
            $output = form_helper::hidden($this->attributes, $this->value);
        break;

      default:;
    }
    $this->output = $output."\n";
  }

}
