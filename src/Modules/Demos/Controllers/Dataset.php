<?php

namespace Modules\Demos\Controllers;

class Dataset extends \Rapyd\Controller
{

    public function indexAction()
    {
        //dataset widget 
        $ds = $this->set->createBuilder();
        $ds->setSource("demo_articles");
        $ds->setPagination(10);
        $ds->getSet();   

        $this->render('Set', array('ds' => $ds));
    }

}