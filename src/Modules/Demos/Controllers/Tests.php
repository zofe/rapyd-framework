<?php

namespace Modules\Demos\Controllers;

class Tests extends \Rapyd\Controller
{

    public function twigAction()
    {
        //todo: rapyd widgets will support twig syntax this way: 

        $env = new \Twig_Environment(new \Twig_Loader_String(), array('cache' => false, 'autoescape' => false, 'optimizations' => 0));
        echo $env->loadTemplate(' {{ title }} {{ subtitle }} ')->render(array('title' => 'foo', 'subtitle' => 'bar'));
    }

}

