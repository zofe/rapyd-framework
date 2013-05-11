<?php

namespace Rapyd\Widgets;

use Rapyd\Widgets\DataGrid\Column as Column;

class DataGrid extends DataSet
{

    protected $fields = array();
    public $columns = array();
    public $rows = array();
    public $output = "";


    
    
    public function setColumn($name, $label = null, $orderby = null)
    {
        $config['pattern'] = $name;
        $config['label'] =  ($label != "") ? $label : $name;
        if ($orderby) $config['orderby'] = $orderby;

        $column = new Column($config);
        $this->columns[] = $column;
        return $column;
    }

    protected function buildGrid()
    {
        $data = get_object_vars($this);
        $data['container'] = $this->button_containers();

        foreach ($this->data as $tablerow) {
            unset($row);
            foreach ($this->columns as $column) {
                
                unset($cell);
                $column->resetPattern();
                $column->setRow($tablerow);
                
				$cell = get_object_vars($column);
				$cell["value"] = $column->getValue();
				$cell["type"] = $column->column_type;
				$row[] = $cell;
            }
            $this->rows[] = $row;
        }

        //switch to Rapyd/Views path 
        if (true) {
            $view = 'DataGrid.twig';
            $current_template = $this->app->view()->getTemplatesDirectory();
            $this->app->config('templates.path', __DIR__ . '/../Views');
            $this->app->setupView();
            $this->app->view()->appendData(array('dg' => $this));
            $output = $this->app->view()->render($view);
            $this->app->config('templates.path', $current_template);
            $this->app->setupView();
            return $output;
        }
        return $this->app->view()->render($view);
    }

    // --------------------------------------------------------------------
    public function build($type = 'Grid')
    {
        parent::build();
        //sniff and perform action
        //$this->sniff_action();
        foreach ($this->columns as & $column) {
            if (isset($column->orderby)) {
                $column->orderby_asc_url = $this->orderby_link($column->orderby, 'asc');
                $column->orderby_desc_url = $this->orderby_link($column->orderby, 'desc');
            }
        }
        $method = 'build' . $type;
        $this->output = $this->$method();
    }

}
