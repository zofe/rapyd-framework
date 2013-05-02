<?php

$users = '\\Modules\\Users\\Controllers\\Users';


$app->addRoutes(array(
    '/users' => $users . ':index',
    '/users/count' => $users . ':count',
        ), function() use ($app) {
            $app->view()->setTemplatesDirectory(realpath(__DIR__ . '/../Views'));
});

