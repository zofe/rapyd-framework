<?php

namespace Rapyd\Helpers;

class Url {

	public static function get_url() {
		$app = Rapyd\Application::getInstance();
		return $app->request()->getUrl();
	}

	public static function qs_append($key, $value, $url = null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();
		if (strpos($url, '?') !== false) {
			$qs = substr($url, strpos($url, '?') + 1);
			$url = substr($url, 0, strpos($url, '?'));
			parse_str($qs, $qs_array);
		}
		$qs_array[$key] = $value;
		$query_string = self::unparse_str($qs_array);
		return ($url . $query_string);
	}

	public static function qs_remove($keys, $url = null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();
		if (strpos($url, '?') === false)
			return $url;

		$qs = substr($url, strpos($url, '?') + 1);
		$url = substr($url, 0, strpos($url, '?'));
		parse_str($qs, $qs_array);

		if (!is_array($keys)) {
			if ($keys == 'ALL')
				return $url;
			$keys = array($keys);
		}
		foreach ($keys as $key) {
			unset($qs_array[$key]);
		}
		$query_string = self::unparse_str($qs_array);

		return ($url . $query_string);
	}

	public static function qs_remove_all($toremove, $cid = null, $url = null) {

		if (isset($cid)) {
			foreach ($semantic as $key) {
				$keys[] = $key . $cid;
			}
			$semantic = $keys;
		}
		return self::remove($semantic, $url);
	}

	public static function qs_replace($key, $newkey, $url = null) {
		$qs_array = array();
		$url = (isset($url)) ? $url : self::get_url();
		if (strpos($url, '?') !== false) {
			$qs = substr($url, strpos($url, '?') + 1);
			$url = substr($url, 0, strpos($url, '?'));
			parse_str($qs, $qs_array);
		}
		if (isset($qs_array[$key])) {
			$qs_array[$newkey] = $qs_array[$key];
			unset($qs_array[$key]);
		}
		$query_string = self::unparse_str($qs_array);
		return ($url . $query_string);
	}

	public static function qs_value($key, $default = FALSE) {
		if (strpos($key, '|')) {
			$keys = explode('|', $key);
			foreach ($keys as $k) {
				$v = self::value($k, $default);
				if ($v != $default)
					return $v;
			}
			return $default;
		}
		if (strpos($key, '.')) {
			list($namespace, $subkey) = explode('.', $key);
			return self::param($subkey, $namespace, $default);
		} else {
			return self::param($key, null, $default);
		}

		$app = Rapyd\Application::getInstance();
		$param = $app->request()->get($key);
		if (strpos($key, '.')) {
			list($namespace, $subkey) = explode('.', $key);
			return (isset($param[$namespace][$key])) ? $param[$namespace][$key] : $default;
		} else {
			return (isset($param[$key])) ? $param[$key] : $default;
		}
	}

	public static function unparse_str($array) {
		return '?' . http_build_query($array);
	}

}