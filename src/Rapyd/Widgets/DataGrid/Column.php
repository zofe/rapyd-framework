<?php

namespace Rapyd\Widgets\DataGrid;

use Rapyd\Widgets\Widget;
use Rapyd\Helpers\HTML as HTML;

class Column extends Widget
{

    public $url = "";
    public $link = "";
    public $onclick = "";
    public $label = "";
    public $attributes = array();
    public $tr_attributes = array();
    public $tr_attr = array();
    public $column_type = "normal"; //orderby, detail, ation
    public $orderby = null;
    public $checkbox = "";
    public $check = "";
    public static $checkbox_id = 1;
    public $orderby_asc_url;
    public $orderby_desc_url;
    protected $pattern = "";
    protected $pattern_type = null;
    protected $field = null;
    protected $field_name = null;
    protected $field_list = array();

    // --------------------------------------------------------------------
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->check_pattern();
    }

    protected function check_pattern()
    {
        if (is_object($this->pattern)) {
            $this->pattern_type = "field_object";
            $this->field = $this->pattern;
            if ($this->orderby === true) {
                $this->orderby_field = $this->field->name;
            }
        } else {
            $this->field_list = $this->parser->variables($this->pattern);
            if (is_array($this->field_list)) {
                $this->pattern_type = "pattern";
                if ($this->orderby === true) {
                    $this->orderby_field = $this->field_list[0];
                }
            } else {
                $this->pattern_type = "field_name";
                $this->field_name = $this->pattern;
                if ($this->orderby === true) {
                    $this->orderby_field = (isset($this->orderby_field)) ? $this->orderby_field : $this->field_name;
                    die($this->orderby_field);
                }
            }
        }
        if ($this->orderby) {
            $this->column_type = 'orderby';
        }
    }

    protected function resetPattern()
    {
        $this->rpattern = $this->pattern;
    }

    protected function setPattern($pattern)
    {
        //$this->parser->render($this->pattern, $data_row);
        $this->pattern = $pattern;
    }

    protected function setLabel($label)
    {
        $this->label = $label;
    }

    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        //if ($orderby === true) {
        //    $this->orderby = (isset($this->orderby_field)) ? $this->orderby_field : $this->field_name;
        //}
        return $this;
    }

    protected function setUrl($url, $img = '', $onclick = '')
    {
        $this->url = $url;
        $this->img = $img;
        $this->onclick = $onclick;
        return $this;
    }

    // --------------------------------------------------------------------
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    // --------------------------------------------------------------------
    public function setCallback($callback, $object = null)
    {
        $this->callback = $callback;
        $this->callback_object = $object;
    }

    // --------------------------------------------------------------------
    public function setTRAttributes($attributes)
    {
        $this->tr_attributes = $attributes;
        return $this;
    }

    // --------------------------------------------------------------------
    public function setRow($data_row)
    {
        $data_row = get_object_vars($data_row);
        if (isset($data_row[$this->pattern])) {
            $this->rpattern = $data_row[$this->pattern];
        } else {
            $this->rpattern = $this->parser->render($this->pattern, $data_row);
        }
        if (isset($this->callback_object)) {
            $this->rpattern = call_user_func(array($this->callback_object, $this->callback), $data_row);
        } elseif (isset($this->callback)) {
            $this->rpattern = call_user_func($this->callback, $data_row);
        }
        if ($this->url) {
            if (!isset($this->attributes['style']))
                $this->attributes['style'] = 'width: 70px; text-align:center; padding-right:5px';
            $this->link = $this->parser->render($this->url, $data_row);
        }

        //manage attributes
    }

    /**
     * a column value by default is a string: the field-name you want to show in the column
     * but it support also a "pattern" with html and placeholders like : {field1} <br /> {field2}
     * @return type 
     */
    function getValue()
    {

        if ($this->rpattern == "") {
            $this->rpattern = "&nbsp;";
        }

        return $this->rpattern;
    }

    /**
     * replace {field} with value 
     * @return string
     */
    function orderby_link()
    {
        die('e mo?');
        return str_replace('{field}', $column->orderby_field, $this->orderby_asc_url);
    }

}
