<?php


\Slim\Route::setDefaultConditions(array(
    'widget' =>'(/pag/\d+)?(/orderby/\w+/(asc|desc))?(/pag/\d+)?'
));