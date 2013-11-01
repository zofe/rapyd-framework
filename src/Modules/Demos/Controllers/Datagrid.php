<?php

namespace Modules\Demos\Controllers;

class Datagrid extends \Rapyd\Controller
{

    public function indexAction()
    {

        //dataset widget 
        $dg = $this->grid->createBuilder();
        $dg->setSource("demo_articles");
        $dg->setPagination(10);
        $dg->add('article_id',"ID", true);
        $dg->add('<em>{{ title|lower }}</em>',"title", true);
        $dg->getGrid();   

        $this->render('Grid', array('dg' => $dg));
    }

}