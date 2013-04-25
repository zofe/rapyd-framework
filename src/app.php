<?php

use Symfony\Component\Routing;
#use Symfony\Component\HttpFoundation\Response;
 

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
    'year' => null,
    '_controller' => 'Calendar\\Controller\\LeapYearController::indexAction',
)));
$routes->add('users', new Routing\Route('/users', array(

    '_controller' => 'Users\\Controller\\User::indexAction',
)));
 
return $routes;