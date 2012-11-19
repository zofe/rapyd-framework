<?php


 class rpd_loader_library
 {

	public static function include_paths()
	{
		static $paths = array();
		
		if (count($paths)<1)
		{
			$paths[] = ROOT.'application/';
			$modules = array_diff(scandir(ROOT.'modules/'), array('..', '.'));
			foreach ($modules as $mod)
			{
				$paths[] = ROOT.'modules/'.$mod.'/';
			}
			
			$widgets = array_diff(scandir(ROOT.'widgets/'), array('..', '.'));
			foreach ($widgets as $wdg)
			{
				$paths[] = ROOT.'widgets/'.$wdg.'/';
			}
			$paths[] = ROOT.'base/';
			$paths[] = ROOT.'core/';
		}
		return $paths;
	}

	
	
	public static function include_assets()
	{
		static $paths = array();
		
		if (count($paths)<1)
		{
			$paths[] = DOC_ROOT.'application/';
			$modules = array_diff(scandir(DOC_ROOT.'modules/'), array('..', '.'));
			foreach ($modules as $mod)
			{
				$paths[] = DOC_ROOT.'modules/'.$mod.'/';
			}
			
			$widgets = array_diff(scandir(DOC_ROOT.'widgets/'), array('..', '.'));
			foreach ($widgets as $wdg)
			{
				$paths[] = DOC_ROOT.'widgets/'.$wdg.'/';
			}
			$paths[] = DOC_ROOT.'base/';
			$paths[] = DOC_ROOT.'core/';
		}
		return $paths;
	}
	
	/**
	 * class autoloader
	 *
	 * @param string $class
	 * @return bool loaded or not 
	 */
	public static function auto_load($class)
	{

		$prefix = '';
		$suffix = '';
		if (class_exists($class, FALSE))
			return TRUE;
		if (($pos = strrpos($class, '_')) > 0)
		{
			// Find the class prefix and suffix
			$prefix = substr($class, 0, $pos);
			$suffix = substr($class, $pos + 1);
		}

		$path = '';
		
		$is_core = (substr($class, 0, 4) === 'rpd_') ? true : false;

		$file = str_replace('rpd_', '', $prefix);

		if ($class=='rpd')
		{
			$suffix = 'library';
			$file = 'rpd';
		} elseif ($suffix === 'field')
		{
			$suffix = 'library';
			$path = 'fields/';
		} elseif ($suffix === 'driver')
		{
			$suffix = 'library';
			$path = 'drivers/';
		}
		//hack for CI integration (nota.. i file creati in CI non devono avere suffisso _helper _field ecc..)
		if (@$prefix != 'CI' AND (in_array($suffix, array('helper', 'field', 'driver', 'library', 'controller', 'model')) OR $prefix =='rpd'))
		{

			self::load($suffix, $path . $file, "php", $is_core);
		} else
		{
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * find and load a php file (if controller instancing it)
	 *
	 * @param type $directory
	 * @param type $file_name
	 * @return void 
	 */
	public static function load($directory, $file_name, $ext="php", $is_core=false)
	{
		if ($file_name == 'rpd')
		{
			//die(BASE_PATH.'libraries/rapyd.'.$ext);
			require_once BASE_PATH.'libraries/rpd.'.$ext;
		}

		$file = self::find_file($directory, $file_name, $ext, $is_core);
		if ($file)
		{
			require_once $file;

			//if we loaded a controller, need to save $working_path for speedup views inclusion
			if ($directory == 'controller'){
				rpd::$working_path = dirname($file) . '/';
				rpd::$ob_level = ob_get_level();
			}
			// If name contains segments, get last segment for function and class
			if (preg_match("/^.*\/(\w+)$/", $file_name, $matches))
			{
				$file_name = $matches[1];
			}

			return null;
		}
		else
			error_library::error(ucfirst($directory) . ' file doesn\'t exist: ' . $file_name);
	}



	/**
	 * find a file-path according with configured include_paths 
	 * and passed type and name of file
	 *
	 * @param string $type
	 * @param string $file_name
	 * @param string $ext
	 * @return mixed full file-path (or false on fnf)
	 */
	public static function find_file($type, $file_name, $ext = "php", $is_core=false)
	{
		static $file_cache;
		
		if ($type != "" && !strpos($type,'/'))
			$type = self::plural($type);

		$search = $type . '/' . $file_name . '.' . $ext;
		if (!$is_core && isset($file_cache['paths'][$search]))
			return $file_cache['paths'][$search];

		$file_found = FALSE;
		if ($is_core)
		{
			if (is_file(CORE_PATH . $search))
			{
				$file_found = CORE_PATH . $search;
                $search = 'core/'.$search;
			}
		} else {
			
						


			foreach (self::include_paths() as $path)
			{
				if (is_file( $path . $search))
				{
					$file_found = rtrim($path,'/') . '/' . $search;
					break;
				}
			}

		}

		return $file_cache['paths'][$search] = $file_found;
	}

	
	
	public static function find_asset($search)
	{
		static $assets_cache;

		$qs = parse_url($search, PHP_URL_QUERY);
		if ($qs!=''){
			$search = parse_url($search, PHP_URL_PATH);
			$qs = '?'.$qs;
		}
		
		if (isset($assets_cache['paths'][$search]))
			return $assets_cache['paths'][$search].$qs;

		$file_found = FALSE;
		
		if (file_exists(DOC_ROOT. $search))
		{
			$file_found = $search;
		
			
		} else {
			
			
			foreach (self::include_assets() as $path)
			{
				if (is_file( $path . $search))
				{
					$file_found = rtrim(str_replace(DOC_ROOT,'/',$path),'/') . '/' . $search;
					break;
				}
			}

		}
		$assets_cache['paths'][$search] = $file_found;
		if ($file_found)
			return $file_found.$qs;
		else 
			return FALSE;
		
	}
	
	
	protected static function plural($str)
	{
		if (preg_match('/[sxz]$/', $str) OR preg_match('/[^aeioudgkprt]h$/', $str))
		{
			$str .= 'es';
		} elseif (preg_match('/[^aeiou]y$/', $str))
		{
			$str = substr_replace($str, 'ies', -1);
		} else
		{
			$str .= 's';
		}
		return $str;
	}

}
