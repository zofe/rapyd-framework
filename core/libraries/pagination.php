<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_pagination_library {

	public $items_per_page = 10;
	public $total_items = 0;
	public $hash = '';
	protected static $identifier = 0;
	protected $url;
	public $current_page;
	protected $total_pages;
	protected $current_first_item;
	protected $current_last_item;
	protected $first_page;
	protected $last_page;
	protected $previous_page;
	protected $next_page;
        protected $num_links;


	public function __construct($config = array())
	{
    $this->cid = (isset($config['cid'])) ? $config['cid'] : self::get_identifier();
		if (count($config) > 0)
		{
			$this->initialize($config);
		}
	}

	protected function get_identifier()
	{
		if (self::$identifier<1)
		{
			self::$identifier++;
			return "";
		}
		return (string)self::$identifier++;
	}

	public function initialize($config = array())
	{
                foreach ($config as $key => $value)
                {
                  if (property_exists($this, $key))
                  {
                    $this->$key = $value;
                  }
                }

		if (!isset($this->url))
                $this->url = url_helper::get_url();

		//unset current pagination
                $this->url = url_helper::remove('reset'.$this->cid, $this->url);
                $this->url = url_helper::append('pag'.(string)$this->cid, "{page}", $this->url);
                $this->url = $this->url.$this->hash;

		// Core pagination values
		$this->total_items        = (int) max(0, $this->total_items);
		$this->items_per_page     = (int) max(1, $this->items_per_page);
		$this->total_pages        = (int) ceil($this->total_items / $this->items_per_page);
		
		if (!isset($this->current_page))
			$this->current_page       = (int) min(max(1, url_helper::value('pag'.$this->cid)), max(1, $this->total_pages));
		$this->current_first_item = (int) min((($this->current_page - 1) * $this->items_per_page) + 1, $this->total_items);
		$this->current_last_item  = (int) min($this->current_first_item + $this->items_per_page - 1, $this->total_items);

		// If there is no first/last/previous/next page, relative to the
		// current page, value is set to FALSE. Valid page number otherwise.
	 	$this->first_page         = ($this->current_page == 1) ? FALSE : 1;
		$this->last_page          = ($this->current_page >= $this->total_pages) ? FALSE : $this->total_pages;
		$this->previous_page      = ($this->current_page > 1) ? $this->current_page - 1 : FALSE;
		$this->next_page          = ($this->current_page < $this->total_pages) ? $this->current_page + 1 : FALSE;

                if ($this->num_links)
                {
                    $this->nav_start = (($this->current_page - $this->num_links) > 0) ? $this->current_page - ($this->num_links - 1) : 1;
                    $this->nav_end   = (($this->current_page + $this->num_links) < $this->total_pages) ? $this->current_page + $this->num_links : $this->total_pages;
                } else {
                    $this->nav_start = 1;
                    $this->nav_end   = $this->total_pages;
                }
	}

	public function create_links($view = 'pagination')
	{
		if ($this->total_pages<2) return '';
		return rpd::view($view, get_object_vars($this));
	}

	public function __toString()
	{
		return $this->create_links();
	}

	public function offset()
	{
		return (int) ($this->current_page - 1) * $this->items_per_page;
	}

	public function limit()
	{
		return sprintf(' LIMIT %d OFFSET %d ', $this->items_per_page, $this->offset());
	}



} // End Pagination Class
