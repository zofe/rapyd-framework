<?php

$demos = '\\Modules\\Demos\\Controllers\\';


$app->addRoutes(array(
    '/demo' => $demos . 'Demo:index',
    '/basic/hello' => $demos . 'Basic:index',
        ), function() use ($app) {
            $app->view()->setTemplatesDirectory(realpath(__DIR__ . '/../Views'));
});

/*
 //da utilizzare per le route dei moduli?
 $app->hook('slim.before.dispatch', function () use ($app) {
    $app->view()->setTemplatesDirectory(realpath(__DIR__ . '/../Views'));
});
 */