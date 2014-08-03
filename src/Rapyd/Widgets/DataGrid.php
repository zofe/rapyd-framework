<?php

namespace Rapyd\Widgets;

use Rapyd\Widgets\DataGrid\Column as Column;

class DataGrid extends DataSet
{

    protected $fields = array();
    public $columns = array();
    public $rows = array();
    public $output = "";
    public $row_as = null;

    public function setSource($source, $as=null)
    {

        $this->source = $source;
        if (is_null($as) && is_a($source, "\Illuminate\Database\Eloquent\Builder")) {

            $reflection = new \ReflectionClass(get_class($source->getModel()));
            $this->row_as = strtolower($reflection->getShortName());

        } elseif (is_null($as) && is_a($source, "\Illuminate\Database\Eloquent\Model")) {
            $reflection = new \ReflectionClass(get_class($source));
            $this->row_as = strtolower($reflection->getShortName());
        }

        return $this;
    }

    public function setColumn($name, $label = null, $orderby = false)
    {
        $config['row_as'] = $this->row_as;
        $config['pattern'] = $name;
        $config['label'] = ($label != "") ? $label : $name;
        $config['orderby'] = $orderby;

        $column = new Column($config);
        $this->columns[] = $column;

        return $this;
    }

    public function add($name, $label = null, $orderby = false)
    {
        return $this->setColumn($name, $label, $orderby);
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

        $view = 'DataGrid.twig';
        $this->app->view()->appendData(array('dg' => $this));

        return $this->app->view()->render($view);
    }

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

    public function getGrid($type = 'Grid')
    {
        $this->build($type);

        return $this->output;
    }

}
