<?php


class rpd_router_library
{
	
	/**
	 * run() instance a controller and execute one of it's methods
	 * (using parameters or detecting it by current url), 
	 * if no valid controller/method was found it throw 404 error
	 * 
	 * @param string $controller
	 * @param string $method
	 * @param array $params
	 * @return mixed 
	 */
	public static function run($controller=null, $method=null, $params=array())
	{
		/**
		 * locale settings
		 */
        if (!isset($controller))
        {
            date_default_timezone_set(rpd::$config['timezone']);
            setlocale(LC_TIME, lang_helper::get_lang('locale'), lang_helper::get_lang('locale').".utf8");
        }
		if (isset($controller) && strpos($controller,'{controller}')!==false)
		{
			$controller = str_replace('{controller}', str_replace('_controller', '', get_class(rpd::$main_controller)), $controller);
		}
		
		if (isset($controller) AND is_array($controller))
		{
			$controller = $controller[1];
			rpd::$uri_string = $controller;
		}

		//this case we have a rpd::run('controller/method/..')
		if (isset($controller) AND !isset($method) AND strpos($controller, '/'))
		{
			rpd::$uri_string = $controller;
			rpd::$ruri_string = self::reroute($controller, rpd::config('routes'));
			$segment_arr = explode('/', rpd::$ruri_string);
			while ($segment_arr)
			{

				$path = implode('/', $segment_arr);
				$arr_segments[] = array_pop($segment_arr);

				$controller = loader_library::find_file('controller', $path);
				if ($controller)
				{
					$controller = $path;
					$arr_segments = array_reverse($arr_segments);
					if (isset($arr_segments[1]))
						$method = $arr_segments[1];
					if (isset($arr_segments[2]))
						$params = array_slice($arr_segments, 2);
					break;
				}
			}
			//if controller is still false, i must stop here with empty result, because the resource was not found
			if ($controller === false){
                return false;
            }
		}


		
		
		//controller called using parameters
		if (isset($controller, $method))
		{
			rpd::$uri_string = trim($controller . '/' . $method . '/' . implode('/', $params),'/');
			rpd::$ruri_string = rpd::$uri_string;
			$controller .= '_controller';
			rpd::$controller = new $controller(); //self::load('controller', $controller);
			rpd::$method = str_replace('-', '_', $method);
			rpd::$params = $params;
			rpd::$routed = false;
		}
		//autodetect controller by router
		else
		{
			rpd::$routed = true;
			self::route();
		}

		$controller = rpd::$controller;
		$method = rpd::$method;
		$params = rpd::$params;

		if (is_object($controller))
		{
			if (!isset(rpd::$main_controller))
				rpd::$main_controller = $controller;
			
			$cached = cache_helper::get_cache(rpd::$uri_string);
			if ($cached != '')
			{
				rpd::$cached_files++;
				//is routed? so i'm here from self::run(); need to send output end exit.				
				if (rpd::$routed){
					echo $cached;
					return;
				//i'm here from echo self::run('some/uri') so need to return output to caller
				} else {
					return $cached;
				}

			}

			if (method_exists($controller, $method))
			{


				if (is_callable(array($controller, $method)))
				{
					//if there are params, check they are not more than expected
					if (count($params))
					{
						//basically this stuff remove from params all "widgets" segments-semantic (like pagination, orderby, editing actions, always admitted)
						$uri = implode('/', $params);
						$url = url_helper::remove_all(null, url_helper::url($uri));
						if (lang_helper::get_lang('set')!='')
							$uri = preg_replace('@^'.lang_helper::get_lang('set').'/?(.*)@i', '$2', url_helper::uri($url));
						else 
							$uri = url_helper::uri($url);
						$params = ($uri=='') ? array() : explode('/', $uri);

						$reflector = new ReflectionClass(get_class($controller));
						$default_params_count = count($reflector->getMethod($method)->getParameters());
						if (count($params) > $default_params_count){
							rpd::error(404);
						}
					}
					return call_user_func_array(array($controller, $method), $params);

				} else
				{
					rpd::error(404);
				}
			} elseif (is_callable(array($controller, 'remap')))
			{
				array_unshift($params, $method);
				return call_user_func_array(array($controller, 'remap'), $params);
			}
		}
		rpd::error(404);

	}
	
	
	
	/**
	 * parses current application URI to determine which controller and method to call.
	 * it's called by run() function (inside index.php)
	 *
	 *
	 * @return mixed true on success, 404 page on error  
	 */
	public static function route()
	{
		rpd::$uri_string = url_helper::get_uri();
		//remove if present language segment
		if (lang_helper::get_lang('set')!='')
			rpd::$uri_string =	preg_replace('@^'.lang_helper::get_lang('set').'/?(.*)@i', '$2', rpd::$uri_string);
		rpd::$ruri_string = self::reroute(rpd::$uri_string, rpd::config('routes'));
		
		


		if (!preg_match('/[^A-Za-z0-9\:\;\+\/\.\-\_\#]/i', rpd::$ruri_string) || empty(rpd::$ruri_string))
		{
			if (rpd::$ruri_string == '')
				$segment_arr = array();
			else
				$segment_arr = explode('/', rpd::$ruri_string);
	

			//die(url_helper::get_uri(). " | " . rpd::$ruri_string);	

			
			// defaults
			$controller_name = rpd::config('default_controller');
			$method_name = rpd::config('default_method');
			$params = array();

			// if URL segments exist, overwrite defaults
			$controller = true;
			if (count($segment_arr) > 0)
			{
				$controller = false;
				$arr_segment = array();

				while ($segment_arr)
				{
					$path = implode('/', $segment_arr);
					$arr_segments[] = array_pop($segment_arr);


					// starting from last segment.. searching for a valid controller
					// when is found, next segment is the method and others are params
					$controller = loader_library::find_file('controller', $path);
					if ($controller)
					{
						$controller_name = $path;

						$arr_segments = array_reverse($arr_segments);
						if (isset($arr_segments[1]))
							$method_name = $arr_segments[1];
						if (isset($arr_segments[2]))
							$params = array_slice($arr_segments, 2);

						break;
					}
				}
			}

			
			
			
			if (!$controller)
			{
				rpd::error(404);
			} else
			{

				$controller_name .='_controller';
				rpd::$controller = new $controller_name;
				rpd::$method = str_replace('-', '_', $method_name);
				rpd::$params = $params;

			}
			return true;
		}
		else {
			rpd::error('The URL you entered contains illegal characters.');
			
		}
	}

	/**
	 * reroute function is called by route(), it check routes array to find a route
	 * inspired by caffeinephp
	 * 
	 * @param string $uri
	 * @param array $routes
	 * @return string uri or (if route exists) routed uri 
	 */
	public static function reroute($uri, $routes=array())
	{
		//lang here
		if ($routes)
		{
			foreach ($routes as $regex => $dest)
			{
				$regex = '^' . $regex;
				$regex = str_replace(':num', '[0-9]{1,}', $regex);
				$regex = str_replace(':str', '[A-Za-z]{1,}$', $regex);
				$regex = str_replace(':any', '[A-Za-z0-9-_/]{1,}$', $regex);
				$regex = $regex . '$';
				if (preg_match_all('@' . $regex . '@', $uri, $matches, PREG_SET_ORDER))
				{
					$count = 0;
					foreach ($matches[0] as $match)
					{
						$dest = str_replace('$' . $count, $match, $dest);
						$count++;
					}
					return $dest;
				}
			}
		}
		return $uri;
	}
	

}
