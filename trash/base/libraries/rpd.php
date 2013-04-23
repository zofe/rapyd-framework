<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd extends rpd_rpd_library {

	
	
	public static function find_asset($search)
	{
			return loader_library::find_asset($search);
	}	
	

	public static function lang($key = null, $args = array())
	{
			return lang_helper::lang($key, $args);
	}

	public static function get_lang($value='')
	{
		return lang_helper::get_lang($value);
	}	
	
	
	/**
	 * shortcut for html_helper::anchor
	 * 
	 * @param string $uri
	 * @param string $text
	 * @param string $attributes
	 * @return object 
	 */
	public static function anchor($uri, $text='', $attributes='')
	{
			return html_helper::anchor($uri, $text, $attributes);
	}
	/**
	 * shortcut for html_helper::image
	 *
	 * @param string $path
	 * @return string 
	 */
	public static function image($path, $attrs=array())
	{
		return html_helper::image($path, $attrs);
	}
	
	/**
	 * shortcut for html_helper::head()
	 * 
	 * @return string 
	 */
	public function head()
	{
		return html_helper::head();
	}

	/**
	 * add a css resource link to head section
	 *
	 * @param string $css
	 * @param bool $external
	 * @return void 
	 */
	public function css($css, $external=false)
	{
		return html_helper::css($css, $external);
	}

	/**
	 * add a js resource link to head section
	 *
	 * @param string $css
	 * @param bool $external
	 * @return void 
	 */
	public function js($js, $external=false)
	{
		return html_helper::js($js, $external);
	}
	
	

	/**
	 * shortcut for url_helper::url
	 * 
	 * @param string $uri
	 * @return object 
	 */
	public static function redirect($url, $method = 'location', $http_response_code = 302)
	{
		return url_helper::redirect($url, $method, $http_response_code);
	}

	/**
	 * shortcut for url_helper::current_page
	 * 
	 * @param mixed $page can be an uri path, or an array of
	 * @param string $output
	 * @return string 
	 */
	public static function current_page($page, $output=null)
	{
		return url_helper::current_page($page, $output);
	}
	
	
	/**
	 * shortcut for url_helper::url
	 * 
	 * @param string $uri
	 * @return object 
	 */
	public static function url($uri, $lang=null)
	{
			return url_helper::url($uri, $lang);
	}


	/**
	 * shortcut for url_helper::uri
	 *
	 * @param string $url
	 * @return object 
	 */
	public static function uri($url)
	{
		return url_helper::uri($url);
	}
	
} 
