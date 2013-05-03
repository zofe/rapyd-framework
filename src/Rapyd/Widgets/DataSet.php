<?php

namespace Rapyd\Widgets;


class DataSet extends Widget
{

    public $cid;

    /**
     *
     * @var \Rapyd\Application
     */
    protected $app;
    public $source;

    /**
     *
     * @var \Illuminate\Database\Query\Builder
     */
    public $query;
    public $per_page = 10;
    public $num_links = 8;
    public $data;
    public $hash = '';
    public $url;
    public $current_page;

    /**
     *
     * @var \Rapyd\Widgets\Paginator
     */
    public $pagination;
    public $orderby_field;
    public $orderby_direction;
    protected $type;
    protected $limit;
    protected $orderby;
    public $total_rows;
    protected $orderby_uri_asc;
    protected $orderby_uri_desc;

    // --------------------------------------------------------------------

    public function __construct($config = array())
    {
        parent::__construct($config);

        //inherit cid from datafilter
        if (isset($this->source) AND is_object($this->source)) {
            $this->cid = $this->source->cid;
        }
        //or generate new one
        else {
            $this->cid = parent::get_identifier();
        }
        $this->app = \Rapyd\Application::getInstance();
    }

    // --------------------------------------------------------------------

    public function setSource($source)
    {
        $this->source = $source;
    }

    public function table($table)
    {
        $this->query = $this->app->db->table($table);
    }

    // --------------------------------------------------------------------

    public function orderby_link($field, $direction = "asc")
    {
        $direction = "orderby_uri_" . $direction;
        return str_replace('-field-', $field, $this->$direction);
    }

    // --------------------------------------------------------------------

    public function orderby($field, $direction)
    {
        $this->orderby = array($field, $direction);
    }

    // --------------------------------------------------------------------

    protected function limit($limit, $offset)
    {
        $this->limit = array($limit, $offset);
    }

    // --------------------------------------------------------------------

    public function build()
    {
        if (is_string($this->source) && strpos(" ", $this->source) === false)
        {
            //tablename
            $this->type = "query";
            $this->total_rows = $this->query->count();
        }
        //array
        elseif (is_array($this->source)) {
            $this->type = "array";
            $this->total_rows = count($this->source);
        } 
        //exception


        //offset and pagination setup/detect
        $config = array(
            'cid' => $this->cid,
            'total_items' => $this->total_rows, // use db count query here of course
            'items_per_page' => $this->per_page, // it may be handy to set defaults for stuff like this in config/pagination.php
            'num_links' => $this->num_links,
            'hash' => $this->hash,
            'url' => $this->url,
            'current_page' => $this->current_page,
        );
        $this->pagination = new \Rapyd\Helpers\Pagination($config);
        $offset = $this->pagination->offset();

        $this->limit($this->per_page, $offset);

        //build orderby urls
        $this->orderby_uri_asc = $this->app->url->remove('pag' . $this->cid)->remove('reset' . $this->cid)->append('orderby' . $this->cid, array("-field-", "asc")) . $this->hash;
        $this->orderby_uri_desc = $this->app->url->remove('pag' . $this->cid)->remove('reset' . $this->cid)->append('orderby' . $this->cid, array("-field-", "desc")) . $this->hash;
        
        
        //detect orderby
        $orderby = $this->app->url->value("orderby" . $this->cid);
        if ($orderby) {
            $this->orderby_field = $orderby[0];
            $this->orderby_direction = $orderby[1];
            $this->orderby($this->orderby_field, $this->orderby_direction);
        }

        //build subset of data
        switch ($this->type) {
            case "array":
                //orderby
                if (isset($this->orderby)) {
                    list($field, $direction) = $this->orderby;
                    $column = array();
                    foreach ($this->source as $key => $row) {
                        $column[$key] = $row[$field];
                    }
                    if ($direction == "asc") {
                        array_multisort($column, SORT_ASC, $this->source);
                    } else {
                        array_multisort($column, SORT_DESC, $this->source);
                    }
                }

                //limit-offset
                if (isset($this->limit)) {
                    $this->source = array_slice($this->source, $this->limit[1], $this->limit[0]);
                }
                $data = $this->source;
                break;

            case "query":
                //orderby

                if (isset($this->orderby)) {
                    $this->query->orderBy($this->orderby[0], $this->orderby[1]);
                }
                //limit-offset
                if (isset($this->limit)) {
                    $this->query->skip($this->pagination->offset())->take($this->per_page);
                }
                $data = $this->query->get();
                break;
        }
        if (!$data) {
            $data = array();
        }
        $this->data = $data;
        return $this->data;
    }

}

// End Dataset Class
