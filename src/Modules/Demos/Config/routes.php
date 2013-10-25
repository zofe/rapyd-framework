<?php

$demos = '\\Modules\\Demos\\Controllers\\';


$app->addRoutes(array(
    '/demo' => $demos . 'Demo:index',
    '/demo/hello' => $demos .  'Basic:index',
    '/demo/schema' => $demos . 'Schema:index',
    '/demo/datagrid'=> $demos .'Datagrid:index',
    '/demo/dataset'=> $demos . 'Dataset:index',

    '/demo/forms'   => $demos . 'Forms:index',
    
    '/test/twig'   => $demos . 'Tests:twig',
));
