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

        $data['title'] = 'Schema Builder';
        $data['active'] = 'dataset';
        $data['content_raw'] = $this->fetch('Dataset', array('ds' => $ds));
        $data['code'] = highlight_string(file_get_contents(__FILE__), TRUE);

        $this->render('Demo', $data);
    }

}