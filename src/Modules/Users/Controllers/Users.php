<?php


namespace Modules\Users\Controllers;

use Modules\Users\Models\User;

class Users extends \Rapyd\Controller
{

    public function indexAction()
    {
		//this is eloquent
        $users = User::all();
		
		//this is slim 
		$res = $this->app->response();
		$res['Content-Type'] = 'application/json';
		$res->body($users->toJson());
	}
	
    public function countAction()
    {
		//this is fluent?  maybe..
		$count = $this->app->db->table('users')->count();
        
        
        $this->render('Count', array('count' => $count));
	}
}