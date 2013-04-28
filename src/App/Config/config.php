<?php

$conf = array(
    'debug' => false,
    'templates.path'             =>  __DIR__.'/../Views',
    'controller.class_prefix'    => '\\App\\Controllers',
    'controller.method_suffix'   => 'Action',
    'controller.template_suffix' => 'twig',
);

return $conf;