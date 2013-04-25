<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// load
require_once __DIR__.'/../vendor/autoload.php';


// init app
$app = new \Rapyd\Application(array(
    'templates.path'             =>  __DIR__.'/../src/App/Views',
    'controller.class_prefix'    => '\\App\\Controllers',
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => 'php',
));
$app->addRoutes(array(
    '/'            => 'Home:index',
    '/hello/:name' => 'Home:hello',
	'/users'	   => 'Users:index',
));

$app->run();
