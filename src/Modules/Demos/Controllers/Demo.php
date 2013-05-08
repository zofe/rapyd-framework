<?php


namespace Modules\Demos\Controllers;


class Demo extends \Rapyd\Controller
{

    public function indexAction()
    {
		$data['title'] 		= 'Repyd Demos';
		$data['content'] 	=  $this->fetch('Home');
        $this->render('Demo', $data);
	}

}