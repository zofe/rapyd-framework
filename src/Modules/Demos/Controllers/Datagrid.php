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

 
        
        $data['title'] = 'DataSet Widget';
        $data['active'] = 'grid';
        $data['content_raw'] = $this->fetch('Grid', array('dg' => $dg->output));
        $data['code']  = highlight_string(file_get_contents(__FILE__), TRUE);
        $data['code'] .= htmlentities(file_get_contents(__DIR__.'/../Views/Grid.twig'));
        $this->render('Demo', $data);
    }

}