<?php


namespace App\Controllers;

use App\Models\User;

class Users extends \Rapyd\Controller
{

    public function indexAction()
    {
        $users = User::all();
        echo $users->toJson();
		//$this->render('Home', array('foo' => 'orotound', 'bar' => 'grandios'));
	}
}