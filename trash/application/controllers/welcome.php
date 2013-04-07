<?php

class welcome_controller extends app_controller
{
	public function index()
	{
		$data = array('heading'=>'rapyd framework',
					  'message'=>'welcome on rapyd!');

		
		echo rpd::view('welcome', $data);
	}
	
	
	function menu()
	{
		return;
	}
}

