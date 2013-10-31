<?php

namespace Modules\Demos\Controllers;

class Datagrid extends \Rapyd\Controller
{

    public function indexAction()
    {

        //dataset widget 
        $dg = new \Rapyd\Widgets\DataGrid();
        $dg->source("demo_articles");
        $dg->per_page = 10;
        
        $dg->setColumn('article_id',"ID");
        $dg->setColumn('<em>{{ title|lower }}</em>',"title", true);
        $dg->build();   

        $this->render('Grid', array('dg' => $dg->output));
    }

}