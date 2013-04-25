<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
 
function render_template($request)
{
    extract($request->attributes->all());
    ob_start();
    include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
 
    return new Response(ob_get_clean());
}

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/app.php';
 
$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
$resolver = new HttpKernel\Controller\ControllerResolver();
 

// Bootstrap Eloquent ORM
$connFactory = new \Illuminate\Database\Connectors\ConnectionFactory();
$db = include __DIR__.'/../src/db.php';
$connection  = $connFactory->make($db);
$connResolver = new \Illuminate\Database\ConnectionResolver();
$connResolver->addConnection('default', $connection);
$connResolver->setDefaultConnection('default');
\Illuminate\Database\Eloquent\Model::setConnectionResolver($connResolver);


$framework = new Rapyd\Framework($matcher, $resolver);
$response = $framework->handle($request);
 
$response->send();