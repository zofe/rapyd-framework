<?php 



class Lang {

	public static $lang;

	/**
	 * lang get i18n string in the configured language
	 * 
	 * @param string $key
	 * @param array $args  needed only for string with substitutions placeholders
	 * @return strung  a language string
	 */
	public static function lang($key = null, $args = array())
	{
		if (strpos($key, '.*'))
		{
			$namespace = str_replace('*', '', $key);
			$array = array();
			foreach (self::$lang as $subkey => $value)
			{
				if (strpos($subkey, $namespace) !== false)
				{
					$subkey = str_replace($namespace, '', $subkey);
					$array[$subkey] = $value;
				}
			}
			return $array;
		}
		if ($key == '' OR !isset(self::$lang[$key]))
		{
			return $key;
		}

		$string = self::$lang[$key];
		if ($args == '')
		{
			return $string;
		} else if (is_array($args))
		{
			return vsprintf($string, $args);
		} else
		{
			return sprintf($string, $args);
		}
	}


	public static function getLang($value='')
	{		
		static $array = array();
		static $set;
		static $current;
		
		if ($value=='array' && count($array) ) return $array;
		if ($value=='set' && !is_null($set)) return $set;
		if (in_array($value, array('locale','name','segment','index','dateformat')) && !is_null($current)) return $current[$value];
		if ($value=='' && !is_null($current)) return $current;

		//no static cache?  so cycle languages and uri and fill static vars
		$segments = array();
		
		foreach(rpd::config("languages") as $lang)
		{ 
			if ($lang['segment'] == '')
			{
				$default = $lang;
				continue;
			}
			$segments[$lang['segment']] = $lang;
		}
		$current = $default;
		if (count($segments)>0) //piu' di una lingua
		{
			$set = '('.implode('|',array_keys($segments)).')';
			if (preg_match('@^'.$set.'/?@i', url_helper::get_uri() , $match))
			{
				$current = $segments[$match[1]];
			}
		}

		$array = array_merge(array('default'=>$default), $segments);
		$curr = array_search($current, $array);
		$array[$curr]['is_current'] = true;

		if ($value=='array') return $array;
		if ($value=='set') return $set;
		if (in_array($value, array('locale','name','segment','index'))) return $current[$value];
		return $current;
	}

}
