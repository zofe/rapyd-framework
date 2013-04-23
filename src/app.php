<?php

use Symfony\Component\Routing;
#use Symfony\Component\HttpFoundation\Response;
 

$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
    'year' => null,
    '_controller' => 'Calendar\\Controller\\LeapYearController::indexAction',
)));
 
 
return $routes;