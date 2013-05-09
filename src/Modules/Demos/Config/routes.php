<?php

$demos = '\\Modules\\Demos\\Controllers\\';


$app->addRoutes(array(
    '/demo' => $demos . 'Demo:index',
    '/demo/hello' => $demos .  'Basic:index',
    '/demo/schema' => $demos . 'Schema:index',
    '/demo/dataset'=> $demos . 'Dataset:index',
    '/test/twig'   => $demos . 'Tests:twig',
        ), function() use ($app) {
            $app->view()->setTemplatesDirectory(realpath(__DIR__ . '/../Views'));
});

/*
 //da utilizzare per le route dei moduli?
 $app->hook('slim.before.dispatch', function () use ($app) {
    $app->view()->setTemplatesDirectory(realpath(__DIR__ . '/../Views'));
});
 */