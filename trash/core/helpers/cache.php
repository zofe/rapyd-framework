<?php


class rpd_cache_helper
{

	/**
	 * internal cache related
	 * 
	 * @return cached content 
	 */
	public static function get_cache($uri)
	{
		if (!is_dir(rpd::config('cache_path')) OR !is_writable(rpd::config('cache_path')))
		{
			return FALSE;
		}

		$cache_path = rpd::config('cache_path') . str_replace('/', '.', "_".lang_helper::get_lang('segment').'_'.$uri . '.cache');
		
		if (!@file_exists($cache_path))
		{
			return FALSE;
		}

		if (!$cp = @fopen($cache_path, 'rb'))
		{
			return FALSE;
		}
		flock($cp, LOCK_SH);

		$cache = '';
		if (filesize($cache_path) > 0)
		{
			$cache = fread($cp, filesize($cache_path));
		}

		flock($cp, LOCK_UN);
		fclose($cp);

		if (!preg_match("/(<tstamp>(\d+)<\/tstamp>)/", $cache, $match))
		{
			return FALSE;
		}

		if (time() >= $match['2'])
		{
			@unlink($cache_path);
			return FALSE;
		}
		$output = str_replace($match['0'], '', $cache);
		
		//ob_clean();
		return $output;
	}

	/**
	 * internal cache related
	 * 
	 * @param type $output content to cache
	 * @param type $expiration cache time in seconds
	 * @param type $callback parsing method to execute even if the cache not expired
	 * @return bool if cached or not 
	 */
	public static function set_cache($uri, $output, $expiration=0)
	{
		$cache_path = rtrim(rpd::config('cache_path'), '/');
		

		if (!is_dir($cache_path) OR !is_writable($cache_path))
		{
			//die($cache_path);
			return FALSE;
		}

		$stamp = time() + $expiration;
		$cache_path .= '/' . str_replace('/', '.', "_".lang_helper::get_lang('segment').'_'.$uri . '.cache');

		if (!$cp = fopen($cache_path, 'wb'))
		{
			return FALSE;
		}

		if (flock($cp, LOCK_EX))
		{
			fwrite($cp, '<tstamp>' . $stamp . '</tstamp>'. $output);
			flock($cp, LOCK_UN);
		} else
		{
			return FALSE;
		}
		fclose($cp);
		@chmod($cache_path, 0777);
		return TRUE;
	}
}
