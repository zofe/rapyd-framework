<?php

namespace Modules\Demos\Controllers;

class Dataset extends \Rapyd\Controller
{

    public function indexAction()
    {
        //dataset widget 
        $ds = new \Rapyd\Widgets\DataSet();
        $ds->source("demo_articles");
        $ds->per_page = 10;
        $ds->num_links= 2;
        $ds->build();   

        $this->render('Set', array('ds' => $ds));
    }

}