<?php

namespace App\Controllers;

class Home extends \Rapyd\Controller
{

    public function indexAction()
    {
		 $this->render('Home', array('name' => 'Bello!'));
    }
	
	public function helloAction($name)
	{
		 $this->render('Home', array('name' => $name));
	}
	
	public function qsAction()
	{		
        $this->app->response()->write($this->app->qs->url().'<br />');
        $this->app->response()->write($this->app->qs->replace('key','newkey'));
	}
}