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

        $data['title'] = 'DataSet Widget';
        $data['active'] = 'dataset';
        $data['content_raw'] = $this->fetch('Dataset', array('ds' => $ds));
        $data['code']  = highlight_string(file_get_contents(__FILE__), TRUE);
        $data['code'] .= htmlentities(file_get_contents(__DIR__.'/../Views/Dataset.twig'));
        $this->render('Demo', $data);
    }

}