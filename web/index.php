<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// autoload
require_once __DIR__.'/../vendor/autoload.php';


// init app
$app = new \Rapyd\Application();
$app->run();
