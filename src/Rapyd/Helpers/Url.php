<?php

namespace Rapyd\Helpers;

/**
 * Url Class
 *
 * @package    Rapyd
 * @author     Felice Ostuni
 * @copyright  (c) 2013 Rapyd Team
 * @license    http://www.rapyd.com/license
 */
class Url
{

    public $url;
    public $from = 'uri';
    protected $semantic = array(
                        'search'=>1,
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

    public function __construct($app)
    {
        $this->app = $app;

        return $this;
    }

    public static function unparse_str($array)
    {
        return '?' . preg_replace('/%5B[0-9]+%5D/simU', '[]', http_build_query($array));
    }

    public function set($url)
    {
        $this->url = $url;

        return $this;
    }

    public function get()
    {
        if ($this->url == '') {
            return $this->current();
        } else {
            $url = $this->url;
            $this->url = '';

            return $url;
        }
    }

    public function current()
    {
        //var_dump($this->app->router()->getCurrentRoute()->getPattern());
        //die;

        if (isset($_SERVER['HTTP_X_ORIGINAL_URL']))
            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
        $url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');

        return $url;
    }

    public function append($key, $value)
    {
        return ($this->from == 'uri') ? $this->appendUri($key, $value) : $this->appendQS($key, $value);
    }

    public function remove($key, $params = 1)
    {
        return ($this->from == 'uri') ? $this->removeUri($key, $params) : $this->removeQS($key);
    }

    public function removeAll($key)
    {
        return ($this->from == 'uri') ? $this->removeAllUri($key) : $this->removeAllQS($key);
    }

    public function replace($key, $newkey)
    {
        return ($this->from == 'uri') ? $this->replaceUri($key, $newkey) : $this->replaceQS($key, $newkey);
    }

    public function value($key, $default = false)
    {
        return ($this->from == 'uri') ? $this->valueUri($key, $default) : $this->valueQS($key, $default);
    }

    public function appendQS($key, $value)
    {
        $url = $this->get();
        $qs_array = array();
        if (strpos($url, '?') !== false) {
            $qs = substr($url, strpos($url, '?') + 1);
            $url = substr($url, 0, strpos($url, '?'));
            parse_str($qs, $qs_array);
        }
        $qs_array[$key] = $value;
        $query_string = self::unparse_str($qs_array);
        $this->url = $url . $query_string;

        return $this;
    }

    public function removeQS($keys)
    {
        $qs_array = array();
        $url = $this->get();
        if (strpos($url, '?') === false) {
            $this->url = $url;

            return $this;
        }
        $qs = substr($url, strpos($url, '?') + 1);
        $url = substr($url, 0, strpos($url, '?'));
        parse_str($qs, $qs_array);

        if (!is_array($keys)) {
            if ($keys == 'ALL') {
                $this->url = $url;

                return $this;
            }
            $keys = array($keys);
        }
        foreach ($keys as $key) {
            unset($qs_array[$key]);
        }
        $query_string = self::unparse_str($qs_array);

        $this->url = $url . $query_string;

        return $this;
    }

    public function removeAllQS($cid = null)
    {
        $semantic = array_keys($this->semantic);
        if (isset($cid)) {

            foreach ($semantic as $key) {
                $keys[] = $key . $cid;
            }
            $semantic = $keys;
        }

        return $this->remove($semantic);
    }

    public function replaceQS($key, $newkey)
    {
        $qs_array = array();
        $url = $this->get();
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
        $this->url = $url . $query_string;

        return $this;
    }

    public function valueQS($key, $default = false)
    {
        if (strpos($key, '|')) {
            $keys = explode('|', $key);
            foreach ($keys as $k) {
                $v = $this->valueQS($k, $default);
                if ($v != $default)
                    return $v;
            }

            return $default;
        }

        parse_str(parse_url($this->current(), PHP_URL_QUERY), $params);
        if (strpos($key, '.')) {
            list($namespace, $subkey) = explode('.', $key);

            return (isset($params[$namespace][$key])) ? $params[$namespace][$key] : $default;
        } else {
            return (isset($params[$key])) ? $params[$key] : $default;
        }
    }

    //-------------------------------

    public function appendUri($key, $value = null)
    {

        $url = $this->get();
        $seg_array = explode("/", trim($url, '/'));

        $key_position = array_search($key, $seg_array);
        if ($key_position !== false) {

            array_splice($seg_array, $key_position, count((array) $value) + 1, array_merge((array) $key, array_map('strval', (array) $value)));
        } else {
            $seg_array = array_merge($seg_array, array_merge((array) $key, array_map('strval', (array) $value)));
        }

        $this->url = "/".implode('/', $seg_array);

        return $this;

    }

    public function removeUri($keys, $params = 1)
    {
        $url = $this->get();
        $seg_array = explode("/", trim($url, '/'));

        if (!is_array($keys)) {
            $keys = array($keys);
        }

        foreach ($keys as $key) {
            $key_position = array_search($key, $seg_array);
            if ($key_position !== false) {

                $kkey = preg_replace('@\d*$@', '', $key);
                if (isset($this->semantic[$kkey])) {
                    array_splice($seg_array, $key_position, $this->semantic[$kkey] + 1);
                } else {
                    array_splice($seg_array, $key_position, $params + 1);
                }
            }
        }

        $this->url = "/".implode('/', $seg_array);

        return $this;
    }

    public function removeAllUri($cid = null)
    {
        $url = $this->get();
        $keys = array();
        if (isset($cid)) {
            foreach ($this->semantic as $key => $params) {
                $keys[] = $key . $cid;
            }
            $this->url = $this->removeUri($keys)->get();

        } else {

            $uri = $this->get();
            $seg_array = explode("/", trim($uri, '/'));
            foreach ($this->semantic as $key => $params) {
                if (preg_match_all('@(' . $key . '\d*)@', $url, $matches))
                    foreach ($matches[1] as $mkey) {
                        $this->url = $this->removeUri($mkey, $this->semantic[$key] + 1)->get();
                    }
            }
        }

        return $this;
    }

    public function replaceUri($key, $newkey, $url = null)
    {
        $url = $this->get();
        $seg_array = explode("/", trim($url, '/'));

        $key_position = array_search($key, $seg_array);
        if ($key_position !== false) {
            array_splice($seg_array, $key_position, 1, array($newkey));
        }
        $this->url = "/".implode('/', $seg_array);

        return $this;

    }

    public function valueUri($key, $default = false, $params = 1)
    {

        $url = $this->get();
        $seg_array = explode("/", trim($url, '/'));

        if (strpos($key, '|')) {
            $keys = explode('|', $key);
            foreach ($keys as $k) {
                $v = $this->valueUri($k, $default);
                if ($v != $default)
                    return $v;
            }

            return $default;
        }

        if (strpos($key, '.')) {
            //...
        } else {
            $key_position = array_search($key, $seg_array);
            if ($key_position !== false) {
                if (isset($seg_array[$key_position + 1])) {
                    //..
                    $skey = preg_replace("@([a-z]+)\d@", "\\1", $key);

                    if (isset($this->semantic[$skey])) {
                        if ($this->semantic[$skey] == 0)
                            return true;
                        elseif ($this->semantic[$skey] < 2)
                            return $seg_array[$key_position + 1];
                        else
                            return array_slice($seg_array, $key_position + 1, $this->semantic[$skey]);
                    } else {
                        if ($params == 0)
                            return true;
                        elseif ($params < 2)
                            return $seg_array[$key_position + 1];
                        else
                            return array_slice($seg_array, $key_position + 1, $params);
                    }
                } else {
                    return true;
                }
            }
        }

        return $default;
    }

}
