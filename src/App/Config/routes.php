<?php

$conf = array(
    '/'            => 'Home:index',
    '/hello/:name' => 'Home:hello',
    '/users'	   => '\\Modules\\Users\\Controllers\\Users:index',
    '/users/count' => '\\Modules\\Users\\Controllers\\Users:count',

);

return $conf;