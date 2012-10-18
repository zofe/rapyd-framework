<?php

class welcome_controller extends controller_controller
{
	public function index()
	{
		$data = array('heading'=>'rapyd framework',
					  'message'=>'welcome on rapyd!');

		
		echo rpd::view('welcome', $data);
	}
	
	
	public function mo()
	{
		echo 'mo!';
	}
}

