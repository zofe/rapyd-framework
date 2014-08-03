<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// composer autoload
require_once __DIR__.'/../vendor/autoload.php';

// init app
$app = new \Rapyd\Application();
require __DIR__.'/../src/App/Config/hooks.php';
require __DIR__.'/../src/Rapyd/Config/routes.php';
require __DIR__.'/../src/App/Config/routes.php';
require __DIR__.'/../src/Modules/Demos/Config/routes.php';
//... route for other modules ...

$app->notFound(function () use ($app) {
    $app->render('404.twig');
});
$app->error(function (\Exception $e) use ($app) {
    $app->render('error.twig', array('e'=> $e, 'trace'=>  debug_backtrace()));
});
$app->run();
