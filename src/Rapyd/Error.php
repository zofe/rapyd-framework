<?php

namespace Rapyd;

class Error extends \Slim\Middleware
{
    public function call()
    {
        // Set new error output
        $env = $this->app->environment();
        $env['slim.errors'] = fopen('/path/to/output', 'w');

        // Call next middleware
        $this->next->call();
    }
}
