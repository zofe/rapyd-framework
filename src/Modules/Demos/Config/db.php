<?php

$conf = array(
    'default' => 'sqlite',
    'connections' => array(
        'sqlite' => array(
            'driver'    => 'sqlite',
            'database'  => _DIR_. '../DB/demo.sqlite',
        ),
    ),
);

return $conf;