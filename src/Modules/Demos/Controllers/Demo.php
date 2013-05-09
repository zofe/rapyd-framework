<?php


namespace Modules\Demos\Controllers;


class Demo extends \Rapyd\Controller
{

    public function indexAction()
    {
		$data['title'] 	   = 'Rapyd-Framework Demos';
        $data['active']    = 'index';
		$data['content_markdown']   =  $this->fetch('Index');
        $this->render('Demo', $data);
	}

}