<?php


/**
 * Rapyd Superclass provides all main functions and autoload
 * 
 * @package    Core
 * @author     Felice Ostuni
 * @license    http://www.rapyd.com/license
 */
 class rpd_rpd_library
 {
	public static $config;
	public static $working_path;
	public static $ob_level;
	public static $uri;
	public static $uri_string;
	public static $ruri_string;
	public static $routed = false;
	public static $main_controller;
	public static $controller;
	public static $method;
	public static $params = array();
	public static $db;

	public static $cached_files = 0;
	//public static $error_message = ''; 

	/**
	 * init is called (from index.php or include.php) one time per execution
	 * it parse config.php (from each inclusion path) and load all language/i18n strings
	 * @param array $config 
	 */
	public static function init($config)
	{
		ob_start();
		self::$config = $config;

		//self::$qs = new url_helper(); //keep compatibility
		self::$uri = new url_helper();
		//self::$auth = new auth_library();


		//spostare in un metodo di lang_helper
		include_once CORE_PATH . 'i18n/' . lang_helper::get_lang('locale') . '.php';
		lang_helper::$lang = $lang;

		//init application & modules
		foreach (loader_library::include_paths() as $path)
		{

			if (is_file($path . '/init.php'))
			{
				include_once   $path . '/init.php';
			}
			if (is_file( $path . '/i18n/' . lang_helper::get_lang('locale') . '.php'))
			{
				$lang = array();
				include_once  $path . '/i18n/' . lang_helper::get_lang('locale') . '.php';
				lang_helper::$lang = array_merge(lang_helper::$lang, $lang);
			}
			if (is_file($path . '/config.php'))
			{
				$module = array();
				require_once $path . '/config.php';
				if (isset($module['name'])) {
					self::$config['modules'][$module['name']] = $module;
				}
				//self::$config = array_merge_recursive(self::$config, $config);
			}
		}
	}

	/**
	 * return a config value from (/application/config.php)
	 * 
	 * <code>
	 * self::config('itemname');
	 * </code>
	 * @param string $item
	 * @return mixed item value
	 */
	public static function config($item)
	{
		if (strpos($item, '.') !== false)
		{
			$item_arr = explode('.', $item);
			$path = self::$config;
			foreach ($item_arr as $i)
			{
				if (!isset($path[$i]))
					return false;
				$path = $path[$i];
			}
			return $path;
		}
		if (!isset(self::$config[$item]))
		{
			return FALSE;
		}
		return self::$config[$item];
	}


	
	public static function run($controller=null, $method=null, $params=array())
	{
		return router_library::run($controller, $method, $params);
	}
	

	public static function error($code, $message='')
	{
		return error_library::error($code, $message);
	}

	

	/**
	 * load a view file, passing it an associative array of
	 * php vars we need in page
	 *
	 * @param string $file_name not a full path, just view name
	 * @param array $input_data  array key=>val for subtitutions
	 * @return string the output "parsed" content
	 */
	public static function view($file_name, $input_data=array())
	{
		$rpd = self::$main_controller;

		$input_data = (array) $input_data;
		
		if (strpos($file_name, '/'))
		{
			//die(dirname($file_name).' '.basename($file_name));
			$view_path = loader_library::find_file(dirname($file_name),basename($file_name));
		} else 
		{
            //./modules/users/views/login.php
            //./modules/users/views/logged_info.php
            //echo "cerco vista usando working_path ".$file_name."<br/>\n";
			$filename = str_replace('controllers', 'views', self::$working_path).$file_name;
            //$view_path = "";
            //$view_path = str_replace("./", "", $view_path);
            //echo "quindi provo cambiando il filename in: ".$filename;
            $view_path = loader_library::find_file('view', $filename);
            
            //echo "<br>\n(";
            //var_dump(is_file($view_path));
            //echo ")<br>\n";
            //var_dump(is_file($view_path))."<br>\n";
		}
		
		if (is_a($rpd, 'admin_controller') || is_subclass_of($rpd, 'admin_controller'))
		{ 
			$view_path = loader_library::find_file('view','admin/'.$file_name);
		}
		if ($view_path === false)
		{
            //echo "non ho trovato in: ". getcwd() ."  il file:".$view_path ."<br />";
			$view_path = loader_library::find_file('view', $file_name);
		}
		if ($view_path === false) {

            if (self::config('cms.theme'))
                $view_path = self::config('cms.theme').$file_name.'.php';
		} 

		extract($input_data, EXTR_SKIP);
		if (file_exists($view_path)){
			ob_start();
			include $view_path;
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		} else {
			
			error_library::error(500, 'View file not found: '.$file_name);

		}
			
		/*
		try
		{
			include $view_path;
		}
		catch (Exception $e)
		{
			ob_end_clean();
			throw $e;
		}*/
	}

	/**
	 * benchmarks return execution time or memory usage 
	 * you can use it in views with this placeholder: {time} {memory}
	 * 
	 * @param string $aspect time or memory
	 * @return string 
	 */
	public static function benchmarks($aspect='time')
	{
		if ($aspect == 'time')
		{
			return number_format((microtime(true) - RAPYD_BENCH_TIME), 3) . 'sec';
		} elseif ($aspect == 'memory')
		{
			return number_format((memory_get_usage() - RAPYD_BENCH_MEMORY) / 1024 / 1024, 2) . 'MB';
		}
	}


	
	public static function clear_buffer()
	{
		 return "";
	}

	/**
	 * shutdown handling
	 * 
	 */
	public static function shutdown_handler()
	{
		$error = error_get_last();
		if ($error)
		{
			ob_get_level() and ob_end_clean();
			extract($error, EXTR_SKIP);
			error_library::exception_handler(new ErrorException($message, $type, 0, $file, $line), false);
			return TRUE;
		}

		//todo .. cache replaces here
		$output = ob_get_clean();
		$output = str_replace('{time}', self::benchmarks('time'), $output);
		$output = str_replace('{memory}', self::benchmarks('memory'), $output);
		$output = str_replace('{included_files}', count(get_included_files()), $output);
		$output = str_replace('{cached_files}', self::$cached_files, $output);
		
		while (preg_match_all("/<rpd run=\"([^\"]+)\">/i", $output, $matches))
		{

			foreach ($matches[1] as $id=>$uri)
			{
				$uncached = rpd::run($uri);
				$output = str_replace($matches[0][$id], $uncached, $output);
			}
		}

		if (isset(self::$db)){
			$output = str_replace('{queries}', count(self::$db->queries), $output);
		} else {
			$output = str_replace('{queries}', '0', $output);
		}
        
		//$output = preg_replace_callback('/{run::([^}]+)}/', 'self::gino', $output);
		//call_user_func_array(array($controller, $method), $params);
		
		$level = self::$config['output_compression'];
		if ($level AND ini_get('output_handler') !== 'ob_gzhandler' AND (int) ini_get('zlib.output_compression') === 0)
		{
			if ($level < 1 OR $level > 9)
			{
				$level = max(1, min($level, 9));
			}

			if (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
			{
				$compress = 'gzip';
			}
			elseif (stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== FALSE)
			{
				$compress = 'deflate';
			}
		}

		if (isset($compress) AND $level > 0)
		{
			switch ($compress)
			{
				case 'gzip':
					// Compress output using gzip
					$output = gzencode($output, $level);
				break;
				case 'deflate':
					// Compress output using zlib (HTTP deflate)
					$output = gzdeflate($output, $level);
				break;
			}

			header('Vary: Accept-Encoding');
			header('Content-Encoding: '.$compress);

			if (stripos(PHP_SAPI, 'cgi') === FALSE)
			{
				header('Content-Length: '.strlen($output));
			}
		}

		echo $output;

	}
		
	/**
	 * return a web path of given resource, using self::$working_path
	 *  
	 * @param string $resource
	 * @return string  web path of given resource
	 */
	public static function asset($resource)
	{
		//return APP_PATH . dirname(str_replace(RAPYD_ROOT, '', self::$working_path)) . '/assets/' . $resource;
                return APP_PATH . self::$working_path . '/assets/' . $resource;
		//CI fix
		//return RAPYDASSETS.$resource;
	}



	/**
	 * called inside index.php start a database connection (if defined inside config.php)
	 *
	 * @return object connection resource 
	 */
	public static function connect()
	{

		if (isset(self::$db))
			return;

		$db_class = 'rpd_database_' . self::$config['db']['dbdriver'] . '_driver';
		self::$db = new $db_class();
		self::$db->hostname = self::$config['db']['hostname'];
		self::$db->username = self::$config['db']['username'];
		self::$db->password = self::$config['db']['password'];
		self::$db->database = self::$config['db']['database'];
		self::$db->dbprefix = self::$config['db']['dbprefix'];
		self::$db->dbdriver = self::$config['db']['dbdriver'];
		self::$db->db_debug = self::$config['db']['db_debug'];
		self::$db->db_attached = array();
		$result = self::$db->connect();
		if ($result !== false)
		{
			self::$db->select_db();
		}

		//connect modules (check custom script for each module, to override or attach dbs)
		foreach (loader_library::include_paths() as $path)
		{
			if (is_file( $path . '/connect.php'))
			{
				require_once $path . '/connect.php';
			}
		}

		return $result;
	}



	/**
	 * cache to be used in conjunction of a view:
	 * 
	 * <code>
	 *   echo $this->cache($this->view('viewfile'), 60);
	 *   //this will cache a page for 60 seconds
	 * </code>
	 * 
	 * @param string $output
	 * @param int $expiration
	 * @return string $output or cached content 
	 */
	public static function cache($uri, $output, $expiration)
	{
		cache_helper::set_cache($uri, $output, $expiration);
		return $output;
	}
	
	public function __get($name)
	{
		if (in_array($name, array('db','qs','uri','uri_string','ruri_string','auth')))
			if (isset(self::$$name))
					return self::$$name;
	}
}

