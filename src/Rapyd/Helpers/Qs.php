<?php

namespace Rapyd\Helpers;

class Qs
{

    //to be decoupled from Slim
    public static function url()
    {
        if (isset($_SERVER['HTTP_X_ORIGINAL_URL']))
            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
        $url_string = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');
        return $url_string;
    }

    //to be decoupled from Slim
    public static function uri()
    {
        static $uri_string = '';
        if ($uri_string == '') {
            $uri_string = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
            $uri_string = trim($uri_string, '/');
        }
        return $uri_string;
    }

    public static function append($key, $value, $url = null)
    {
        $qs_array = array();
        $url = (isset($url)) ? $url : self::url();
        if (strpos($url, '?') !== false) {
            $qs = substr($url, strpos($url, '?') + 1);
            $url = substr($url, 0, strpos($url, '?'));
            parse_str($qs, $qs_array);
        }
        $qs_array[$key] = $value;
        $query_string = self::unparse_str($qs_array);
        return ($url . $query_string);
    }

    public static function remove($keys, $url = null)
    {
        $qs_array = array();
        $url = (isset($url)) ? $url : self::url();
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

    public static function removeAll($toremove, $cid = null, $url = null)
    {

        if (isset($cid)) {
            foreach ($semantic as $key) {
                $keys[] = $key . $cid;
            }
            $semantic = $keys;
        }
        return self::remove($semantic, $url);
    }

    public static function replace($key, $newkey, $url = null)
    {
        $qs_array = array();
        $url = (isset($url)) ? $url : self::url();
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

    public static function value($key, $default = FALSE)
    {
        if (strpos($key, '|')) {
            $keys = explode('|', $key);
            foreach ($keys as $k) {
                $v = self::value($k, $default);
                if ($v != $default)
                    return $v;
            }
            return $default;
        }
        $app = \Rapyd\Application::getInstance();
        $param = $app->request()->get($key);
        if (strpos($key, '.')) {
            list($namespace, $subkey) = explode('.', $key);
            return (isset($param[$namespace][$key])) ? $param[$namespace][$key] : $default;
        } else {
            return (isset($param[$key])) ? $param[$key] : $default;
        }
    }

    public static function unparse_str($array)
    {
        return '?' . http_build_query($array);
    }

}