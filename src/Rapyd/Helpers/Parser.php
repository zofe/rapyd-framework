<?php

namespace Rapyd\Helpers;

class Parser
{

    protected $env;

    public function __construct($config = array())
    {
        $this->env = new \Twig_Environment(new \Twig_Loader_String(), array('cache' => false, 'autoescape' => false, 'optimizations' => 0));

        //aggiungere una estensione o filtri custom per abilitare callback da controller/querybuilder/model/viste??
        //studiare http://twig.sensiolabs.org/doc/advanced.html
        //es: $dg->column(' {{ name }} ',"NAME")
        //$this->env->addExtension($extension);        
    }

    ////es: $dg->column('{{ name }}, {{ lastname }}',"NAME")   
    public function parse($pattern, $array)
    {
        return $env->loadTemplate($pattern)->render($array);
    }

}