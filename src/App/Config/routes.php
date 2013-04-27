<?php

$conf = array(
    '/'            => 'Home:index',
    '/hello/:name' => 'Home:hello',
	'/users'	   => '\\Modules\\Users\\Controllers\\Users:index',
);

return $conf;