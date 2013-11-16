<?php

$conf = array(
    'debug' => false,
    'templates.path' => __DIR__ . '/../Views',
    'controller.class_prefix' => '\\App\\Controllers',
    'controller.method_suffix' => 'Action',
    'controller.template_suffix' => 'twig',
    'url_method' => 'uri',
    'timezone' => 'Europe/Rome',
    'languages' => array(
        array('name' => 'english', 'locale' => 'en_US', 'dateformat' => 'm/d/Y', 'segment' => ''),
        array('name' => 'italiano', 'locale' => 'it_IT', 'dateformat' => 'd/m/Y', 'segment' => 'it'),
        array('name' => 'française', 'locale' => 'fr_FR', 'dateformat' => 'd/m/Y', 'segment' => 'fr'),
        array('name' => 'česky', 'locale' => 'cs_CZ', 'dateformat' => 'd.m.Y', 'segment' => 'cs')
    ),
);

return $conf;