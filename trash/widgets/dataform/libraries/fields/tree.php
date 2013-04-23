<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class tree_field extends field_field {

	public $type = "select";
	public $description = "";
	public $clause = "where";
	public $css_class = "select";

	public $tree_table;
	public $tree_cat_id;
	public $tree_parent_id;
	public $tree_label;
	public $tree_hide_itself;
	public $empty_option = false;


	// --------------------------------------------------------------------

  function get_value()
  {
    parent::get_value();
	
       
	$tree = new tree_helper();
	$tree->table 		   = $this->tree_table;
	$tree->field_cat_id    = $this->tree_cat_id;
	$tree->field_parent_id = $this->tree_parent_id;
	$tree->field_label     = $this->tree_label;
	$tree->get_tree(0);

	if ($this->empty_option)
			$tree->options[0] = '';
	$this->options = $tree->options;
    if (count($this->options))
    {
        foreach ($this->options as $value=>$description)
        {
          if ($this->value == $value)
          {
            $this->description = $description;
          }
        }  
    }
        
 
  }

	// --------------------------------------------------------------------

  function build()
  {
   $output = "";
    if(!isset($this->style)&& !isset($this->attributes['style']))
		{
      $this->style = "width:290px;";
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
        } else {
          $output = $this->description;
        }
        break;

      case "create":
      case "modify":

		$disabled = 'null';
        if (isset($this->tree_hide_itself) && $this->status == 'modify')
		{
			$disabled = $this->model->get($this->tree_cat_id);
		}
         $output = form_helper::dropdown($this->attributes, $this->options, $this->value, '', $disabled). $this->extra_output;
        break;

      case "hidden":
        $output = form_helper::hidden($this->name, $this->value);
        break;

      default:
    }
    $this->output = $output;
  }

}
