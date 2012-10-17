<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');



class rpd_url_helper {


	public static $lang = '';
	//clause, and params expected   i.e.  for:  orderby/{field}/{direction}   ordeby need 2 params
	public static $semantic = array('search'=>1,
									'reset'=>1,
									'pag'=>1,
									'orderby'=>2,
									'show'=>1,
									'create'=>1,
									'modify'=>1,
									'delete'=>1,
									'insert'=>1,
									'update'=>1,
									'do_delete'=>1,
									'process'=>1);

	public static function get_url() {
		if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
		$url_string = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');
		return $url_string;
	}

	// --------------------------------------------------------------------

	public static function get_uri($with_lang=true) {
		static $uri_string = '';
		
		if ($uri_string=='')
		{
			$uri_string = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
			$uri_string = trim($uri_string,'/');
		}
		if (!$with_lang && lang_helper::get_lang('set')!='') $uri_string = preg_replace('@^'.lang_helper::get_lang('set').'/?(.*)@i', '$2', $uri_string);
		return $uri_string;
	}

	// --------------------------------------------------------------------

	public static function get_qs() {
		return $_SERVER['QUERY_STRING'];
	}

	
	// --------------------------------------------------------------------

	/**
	 * take an 'application' URI (ie. controller/method/param)
	 * and return a full URL  (ie. http://host/rapydpath/[index.php]/controller/method/param)
	 */
	public static function url($uri, $lang=null)
	{
		$language_seg = '';
		if (is_null($lang))
			$language_seg = lang_helper::get_lang('segment');
		elseif ($lang!='')
			$language_seg = $lang;
		
		if ($language_seg!='')
			$language_seg = rtrim($language_seg,'/').'/';

		if (lang_helper::get_lang('set')!='')
			$uri = preg_replace('@^'.lang_helper::get_lang('set').'/?(.*)@i', '$2', $uri);
		if (defined('CI_VERSION')) {
			$config = get_config();
			$base_url = $config['base_url'];
			$index_page = $config['index_page'];
			$language_seg = '';
		} else {
			$base_url = rpd::config('basename');
			$index_page = rpd::config('index_page');
		}
		if($base_url!='')
			$uri = preg_replace("#^".preg_quote(trim($base_url,'/').'/')."(.*)#",'\\1',$uri);
		if ($index_page!='')
			return rtrim($base_url,'/').'/'.$index_page.'/'.$language_seg.trim($uri,'/');
		else
			return rtrim($base_url,'/').'/'.$language_seg.trim($uri,'/');
	}


	/**
	 *reverse or url(), return an application uri removing [http://host/path/index.php/]uri
	 *
	 */
	public static function uri($url, $as_array=false)
	{
		if (defined('CI_VERSION')) {
			$config = get_config();
			$base_url = $config['base_url'];
			$index_page = $config['index_page'];
		} else {
			$base_url = rpd::config('basename');
			$index_page = rpd::config('index_page');
		}
		$base_arr = parse_url(rtrim($base_url,'/').'/'.$index_page.'/');
		$url_arr = parse_url($url);
		$uri = ($base_arr['path'] != '/') ? trim(str_replace($base_arr['path'], '', $url_arr['path']), '/') : '';
		return ($as_array) ? explode("/",$uri) : $uri;
	}

	// --------------------------------------------------------------------

	public static function get_self() {
		$url = self::get_url();
		if (strpos($url, '?') === false)  return $url;
		return substr($url, 0, strpos($url,'?'));
	}

	// --------------------------------------------------------------------

	//l'opposto di parse_str() in php non esiste come funziona nativa
	public static function unparse_str($array) {
		$query_string = '?';
		foreach($array as $key => $val) {
			if (is_array($val)) {
				foreach($val as $sub_key => $sub_val) {
					// Integer subkeys are numerically indexed arrays
					$sub_key = is_int($sub_key) ? '[]' : '['.$sub_key.']';
					$query_string .= $key.rawurlencode($sub_key).'='.rawurlencode($sub_val).'&';
				}
			}
			else {
				$query_string .= $key.'='.rawurlencode($val).'&';
			}
		}
		$query_string = rtrim($query_string, '&');
		return $query_string;
	}

	// --------------------------------------------------------------------

	public static function append($key, $value, $url=null) {
		return (rpd::config('url_method')=='uri') ? self::uri_append($key, $value, $url) :  self::qs_append($key, $value, $url);
	}

	public static function remove($keys, $url=null, $params=null) {
		return (rpd::config('url_method')=='uri') ? self::uri_remove($keys, $url, $params) :  self::qs_remove($keys, $url);
	}

	public static function remove_all($cid=null, $url=null) {
		return (rpd::config('url_method')=='uri') ? self::uri_remove_all($cid, $url) :  self::qs_remove_all($cid, $url);
	}

	public static function replace($key, $newkey, $url=null) {
		return (rpd::config('url_method')=='uri') ? self::uri_replace($key, $newkey, $url) :  self::qs_replace($key, $newkey, $url);
	}

	public static function value($key, $default=FALSE, $params=null) {
		return (rpd::config('url_method')=='uri') ? self::uri_value($key, $default, $params) :  self::qs_value($key, $default);
	}

	// --------------------------------------------------------------------

	//vedere se è il caso di usare le rwrules per ovviare ai problemi con i namespace
	public static function qs_append($key, $value, $url=null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();
		if (strpos($url, '?') !== false) {
			$qs = substr($url, strpos($url,'?')+1);
			$url = substr($url, 0, strpos($url,'?'));
			parse_str($qs, $qs_array);
		}
		$qs_array[$key] = $value;

		$query_string = self::unparse_str($qs_array);

		return ($url . $query_string);
	}

	// --------------------------------------------------------------------

	public static function qs_remove($keys, $url=null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();
		if (strpos($url, '?') === false)  return $url;

		$qs = substr($url, strpos($url,'?')+1);
		$url = substr($url, 0, strpos($url,'?'));
		parse_str($qs, $qs_array);

		if (!is_array($keys)) {
			if ($keys=='ALL')
				return $url;
			$keys = array($keys);
		}
		foreach ($keys as $key) {
			unset($qs_array[$key]);
		}
		$query_string = self::unparse_str($qs_array);

		return ($url . $query_string);
	}

	// --------------------------------------------------------------------

	public static function qs_remove_all($cid=null, $url=null) {
		$semantic = array(  'search', 'reset',   'checkbox',
				'pag',    'orderby', 'show',
				'create', 'modify',  'delete',
				'insert', 'update',  'do_delete' );

		if (isset($cid)) {
			foreach ($semantic as $key) {
				$keys[] = $key.$cid;
			}
			$semantic = $keys;
		}
		return self::remove($semantic, $url);
	}

	// --------------------------------------------------------------------

	public static function qs_replace($key, $newkey, $url=null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();

		if (strpos($url, '?') !== false) {
			$qs = substr($url, strpos($url,'?')+1);
			$url = substr($url, 0, strpos($url,'?'));
			parse_str($qs, $qs_array);
		}
		if (isset($qs_array[$key])) {
			$qs_array[$newkey] = $qs_array[$key];
			unset($qs_array[$key]);
		}
		$query_string = self::unparse_str($qs_array);
		return ($url . $query_string);
	}

	// --------------------------------------------------------------------

	public static function qs_value($key, $default=FALSE) {
		if (strpos($key,'|')) {
			$keys = explode('|',$key);
			foreach ($keys as $k) {
				$v = self::value($k, $default);
				if ($v != $default) return $v;
			}
			return $default;
		}

		if (strpos($key,'.')) {
			list($namespace, $subkey) = explode('.',$key);
			return (isset($_GET[$namespace][$subkey])) ?  $_GET[$namespace][$subkey] : $default;
		}
		else {
			return (isset($_GET[$key])) ? $_GET[$key] : $default;
		}
	}

	// --------------------------------------------------------------------

	public static function uri_append($key, $value=null, $url=null) {

		$url = (isset($url)) ? $url : self::get_url();
		$seg_array = self::uri($url, true);

		$key_position = array_search($key,$seg_array);
		if ($key_position!==false) {

			array_splice($seg_array, $key_position,count((array)$value)+1, array_merge((array)$key, array_map('strval', (array)$value)));
		} else {
			$seg_array = array_merge($seg_array, array_merge((array)$key, array_map('strval', (array)$value)));
		}

		return self::url(implode('/',$seg_array));
	}

	// --------------------------------------------------------------------

	public static function uri_remove($keys, $url=null, $params=1) {
		$url = (isset($url)) ? $url : self::get_url();
		$seg_array = self::uri($url, true);

		if (!is_array($keys)) {
			//if ($keys=='ALL')
			//	return $uri;
			$keys = array($keys);
		}

		foreach ($keys as $key) {
			$key_position = array_search($key,$seg_array);
			if ($key_position!==false) {
				
				$kkey = preg_replace('@\d*$@', '', $key);
				if (isset(self::$semantic[$kkey])) {				
					array_splice($seg_array, $key_position,self::$semantic[$kkey]+1);

				} else {
					array_splice($seg_array, $key_position, $params+1);
				}
			}
		}
		return self::url(implode('/',$seg_array));
	}

	// --------------------------------------------------------------------

	public static function uri_remove_all($cid=null, $url=null) {
		$keys = array();
		if (isset($cid)) {
			foreach (self::$semantic as $key=>$params) {
				$keys[] = $key.$cid;
			}
			return self::uri_remove($keys, $url);

		} else {

			$url = (isset($url)) ? $url : self::get_url();
			foreach (self::$semantic as $key=>$params) {
				if (preg_match_all('@('.$key.'\d*)@', $url, $matches))
					foreach($matches[1] as $mkey)
					{
						$url = self::uri_remove($mkey, $url, self::$semantic[$key]+1);
					}
			}
			return $url;
		}

	}

	// --------------------------------------------------------------------

	public static function uri_replace($key, $newkey, $url=null) {
		$url = (isset($url)) ? $url : self::get_url();
		$seg_array = self::uri($url, true);

		$key_position = array_search($key,$seg_array);
		if ($key_position!==false) {
			array_splice($seg_array, $key_position,1, array($newkey));
		}
		return self::url(implode('/',$seg_array));
	}

	// --------------------------------------------------------------------

	public static function uri_value($key, $default=FALSE, $params=1) {

		$uri = self::get_uri();
		$seg_array = explode("/",trim($uri,'/'));

		if (strpos($key,'|')) {
			$keys = explode('|',$key);
			foreach ($keys as $k) {
				$v = self::uri_value($k, $default);
				if ($v != $default) return $v;
			}
			return $default;
		}

		//non mi ricordo l'utilità.. forse nelle chiavi multiple (es. tabelle con pk a due campi)?
		if (strpos($key,'.')) {
			//...
		}
		else {
			$key_position = array_search($key,$seg_array);
			if ($key_position!==false) {
				if (isset($seg_array[$key_position+1])) {
					//..
					$skey = preg_replace("@([a-z]+)\d@", "\\1", $key);

					if (isset(self::$semantic[$skey])) {
						if (self::$semantic[$skey] == 0)
							return true;
						elseif(self::$semantic[$skey] < 2)
							return $seg_array[$key_position+1];
						else
							return array_slice($seg_array, $key_position+1, self::$semantic[$skey]);

					} else {
						if ($params == 0)
							return true;
						elseif($params < 2)
							return $seg_array[$key_position+1];
						else
							return array_slice($seg_array, $key_position+1, $params);
					}
				} else {
					return true;
				}
			}

		}

		return $default;
	}
	
	
	public static function redirect($url, $method = 'location', $http_response_code = 302)
	{
		if ( ! preg_match('#^https?://#i', $url))
		{
			$url = self::url($url);
		}
		
		switch($method)
		{
			case 'refresh'	: header("Refresh:0;url=".$url);
				break;
			default			: header("Location: ".$url, TRUE, $http_response_code);
				break;
		}
		exit;
	}
	
	
	public static function current_page($page, $output=null)
	{
		$is_current_page = false;
		$pages = (array)$page;

		foreach ($pages as $page)
		{
			if ($page == '')
			{			
				$is_current_page = (self::get_uri()=='') ? true : false;
				break;
			} elseif (preg_match('#'.$page.'#',self::get_uri()))
			{
				$is_current_page = true;
				break;
			}
		}
		return (isset($output) && $is_current_page) ? $output : $is_current_page;
	}
	
	
	public static function url_title($str)
	{
		$utf8_dict = array(
		"\xC3\x80" => "A", "\xC3\x81" => "A", "\xC3\x82" => "A", "\xC3\x83" => "A", "\xC3\x84" => "A", 
		"\xC3\x85" => "A", "\xC3\x86" => "A", "\xC3\x9E" => "B", "\xC3\x87" => "C", "\xC4\x86" => "C", 
		"\xC4\x8C" => "C", "\xC4\x90" => "Dj", "\xC3\x88" => "E", "\xC3\x89" => "E", "\xC3\x8A" => "E", 
		"\xC3\x8B" => "E", "\xC4\x9E" => "G", "\xC3\x8C" => "I", "\xC3\x8D" => "I", "\xC3\x8E" => "I", 
		"\xC3\x8F" => "I", "\xC4\xB0" => "I", "\xC3\x91" => "N", "\xC3\x92" => "O", "\xC3\x93" => "O", 
		"\xC3\x94" => "O", "\xC3\x95" => "O", "\xC3\x96" => "O", "\xC3\x98" => "O", "\xC3\x9F" => "Ss",
		"\xC3\x99" => "U", "\xC3\x9A" => "U", "\xC3\x9B" => "U", "\xC3\x9C" => "U", "\xC3\x9D" => "Y", 
		"\xC3\xA0" => "a", "\xC3\xA1" => "a", "\xC3\xA2" => "a", "\xC3\xA3" => "a", "\xC3\xA4" => "a", 
		"\xC3\xA5" => "a", "\xC3\xA6" => "a", "\xC3\xBE" => "b", "\xC3\xA7" => "c", "\xC4\x87" => "c", 
		"\xC4\x8D" => "c", "\xC4\x91" => "dj","\xC3\xA8" => "e", "\xC3\xA9" => "e", "\xC3\xAA" => "e", 
		"\xC3\xAB" => "e", "\xC3\xAC" => "i", "\xC3\xAD" => "i", "\xC3\xAE" => "i", "\xC3\xAF" => "i", 
		"\xC3\xB0" => "o", "\xC3\xB1" => "n", "\xC3\xB2" => "o", "\xC3\xB3" => "o", "\xC3\xB4" => "o", 
		"\xC3\xB5" => "o", "\xC3\xB6" => "o", "\xC3\xB8" => "o", "\xC5\x94" => "R", "\xC5\x95" => "r", 
		"\xC5\xA0" => "S", "\xC5\x9E" => "S", "\xC5\xA1" => "s", "\xC3\xB9" => "u", "\xC3\xBA" => "u", 
		"\xC3\xBB" => "u", "\xC3\xBC" => "ue","\xC3\xBD" => "y", "\xC3\xBD" => "y", "\xC3\xBF" => "y", 
		"\xC5\xBD" => "Z", "\xC5\xBE" => "z");
		$str = strtr($str, $utf8_dict);
		$str = strtolower(utf8_decode($str));
		return preg_replace(array('/[^a-z0-9\:\;+\s]/', '/[\s]+/'), array('', '-'), $str);
	}
	
	public static function deurl_title($str)
	{
		$trans = array(
			'-'   => ' ',
			'_'   => ' ',
		);
		foreach ($trans as $key => $val)
			$str = preg_replace("#".$key."#i", $val, $str);

		$str = ucfirst($str);
		return $str;
	}
        
	public static function array_to_url_title($array)
	{
                unset($array["btn_submit"]);
                $str = urldecode(http_build_query($array));
                //die($str);
		$find    = array('&','=','?','/','-','.');
		$replace = array(';',':',' ',' ',' ',' ');
                $str = str_replace($find, $replace, $str);
                $str = utf8_romanize($str);
                $str = trim(self::url_title($str),'-');

		return $str;
	}

}
