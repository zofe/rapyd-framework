<?php


namespace Rapyd\Helpers;



class Url
{
    public $url;
    protected $from ='qs';
    protected $semantic = array('search', 'reset',   'checkbox',
				'pag',    'orderby', 'show',
				'create', 'modify',  'delete',
				'insert', 'update',  'do_delete');
    
    public function __construct()
    {
        return $this;
    }
    public function set($url)
    {
        $this->url = $url;
        return $this;
    }
    
    public function get()
    {
        if ($this->url=='') {
            return $this->current();
        } else {
            $url = $this->url;
            $this->url = '';
            return $url;
        }
    }
    
    public function current()
    {
        if (isset($_SERVER['HTTP_X_ORIGINAL_URL']))
            $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
        $url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : @getenv('REQUEST_URI');
        return $url;
    }

    
	public function append($key, $value) {
		return ($this->from =='uri') ? $this->appendUri($key, $value) : $this->appendQS($key, $value); 
	}

	public function remove($key, $params=1) {
		return ($this->from =='uri') ? $this->removeUri($key, $params) :  $this->removeQS($key);
	}

	public function removeAll($key) {
		return ($this->from =='uri') ? $this->removeAllUri($key) :  $this->removeAllQS($key);
	}
    
	public function replace($key, $newkey) {
		return ($this->from =='uri') ? $this->replaceUri($key, $newkey) :  $this->replaceQS($key, $newkey);
	}

	public function value($key, $default=FALSE) {
		return ($this->from =='uri') ? $this->valueUri($key, $default) :  $this->valueQS($key, $default);
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
                $this->url =  $url;
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
        $semantic = $this->semantic;
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

    public function valueQS($key, $default = FALSE)
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

    public static function unparse_str($array)
    {
        return '?' . preg_replace('/%5B[0-9]+%5D/simU', '[]', http_build_query($array));
    }

}