<?php

namespace Rapyd;

use \Closure;

class Event
{

    protected static $events = array();

    protected function __construct()
    {
        
    }

    public static function register($name, Closure $closure)
    {
        static::$events[$name][] = $closure;
    }

    public static function registered($name)
    {
        return isset(static::$events[$name]);
    }

    public static function clear($name = null)
    {
        if ($name === null) {
            static::$events = array();
        } else {
            unset(static::$events[$name]);
        }
    }

    public static function override($name, Closure $closure)
    {
        static::clear($name);

        static::register($name, $closure);
    }

    public static function trigger($name, array $params = array(), $break = false)
    {
        $values = array();

        if (isset(static::$events[$name])) {
            foreach (static::$events[$name] as $event) {
                $values[] = $last = call_user_func_array($event, $params);

                if ($break && $last === false) {
                    return $values;
                }
            }
        }

        return $values;
    }

    public static function first($name, array $params = array(), $break = false)
    {
        $results = static::trigger($name, $params, $break);

        return empty($results) ? null : $results[0];
    }

}
