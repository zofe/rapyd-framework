<?php


namespace Modules\Users\Controllers;

use Modules\Users\Models\User;

class Users extends \Rapyd\Controller
{

    public function indexAction()
    {
        $users = User::all();
        echo $users->toJson();
		//$this->render('Home', array('foo' => 'orotound', 'bar' => 'grandios'));
	}
	
    public function countAction()
    {
		echo $this->db->table('users')->count();
	}
}