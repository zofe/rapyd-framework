<?php


$app->addRoutes(array(
    '/users'       => '\\Modules\\Users\\Controllers\\Users:index',
    '/users/count' => '\\Modules\\Users\\Controllers\\Users:count',
),  function() use ($app) {
    
    $app->view()->setTemplatesDirectory(realpath(__DIR__.'/../Views'));
   // $app->config('templates.path', __DIR__.'/../Views');
});