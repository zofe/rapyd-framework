<?php

namespace Rapyd\Helpers;

class Pagination
{

    public $items_per_page = 10;
    public $total_items = 0;
    public $hash = '';
    protected static $identifier = 0;
    public $url;
    public $current_page;
    public $total_pages;
    public $current_first_item;
    public $current_last_item;
    public $first_page;
    public $last_page;
    public $previous_page;
    public $next_page;
    public $num_links;

    /**
     *
     * @var \Rapyd\Application
     */
    public $app;

    public function __construct($config = array())
    {
        $this->cid = (isset($config['cid'])) ? $config['cid'] : self::get_identifier();
        if (count($config) > 0) {
            $this->initialize($config);
        }
    }

    protected function getIdentifier()
    {
        if (self::$identifier < 1) {
            self::$identifier++;
            return "";
        }
        return (string) self::$identifier++;
    }

    public function initialize($config = array())
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        if (!isset($this->app))
            $this->app = \Rapyd\Application::getInstance();


        //unset current pagination
        $this->url = $this->app->url->remove('reset' . $this->cid)
                        ->append('pag' . $this->cid, "-pag-")
                        ->get() . $this->hash;


        // Core pagination values
        $this->total_items = (int) max(0, $this->total_items);
        $this->items_per_page = (int) max(1, $this->items_per_page);
        $this->total_pages = (int) ceil($this->total_items / $this->items_per_page);

        if (!isset($this->current_page))
            $this->current_page = (int) min(max(1, $this->app->url->value('pag' . $this->cid)), max(1, $this->total_pages));
        $this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
        $this->current_last_item = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);

        // If there is no first/last/previous/next page, relative to the
        // current page, value is set to FALSE. Valid page number otherwise.
        $this->first_page = ($this->current_page == 1) ? FALSE : 1;

        $this->last_page = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
        $this->previous_page = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
        $this->next_page = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;

        if ($this->num_links) {
            $this->nav_start = (($this->current_page - $this->num_links) > 0) ? $this->current_page - ($this->num_links - 1) : 1;
            $this->nav_end = (($this->current_page + $this->num_links) < $this->total_pages) ? $this->current_page + $this->num_links : $this->total_pages;
        } else {
            $this->nav_start = 1;
            $this->nav_end = $this->total_pages;
        }
    }

    public function links()
    {
        if ($this->total_pages < 2)
            return '';

        $view = 'Pagination.twig';
        $this->app->view()->appendData(get_object_vars($this));
        $output = $this->app->view()->render($view);
        return $output;
    }

    public function __toString()
    {
        return $this->links();
    }

    public function offset()
    {
        return (int) ($this->current_page - 1) * $this->items_per_page;
    }

}
