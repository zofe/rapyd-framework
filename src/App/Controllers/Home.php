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
    
	public function datasetAction()
	{	
        echo "mo ";
        //$this->app->db->setPaginator(new \Rapyd\Widgets\Paginator('', array('uno','due'), 2, 1));
        $users = $this->app->db->table('users')->paginate(15);
        //$pg = new \Rapyd\Widgets\Paginator('', $users, count($users), 15);
        //die('conto'.$pg->count());
                 //$this->app->db->table('users')->count();
        /*$ds = new \Rapyd\Widgets\DataSet("SELECT * FROM users");
        $ds->db->table("users")->paginate(10);
        $ds->build();*/
        $this->render('dataset', array('users' => $users));
	}
}