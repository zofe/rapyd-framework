<?php


class demo_controller extends app_controller {

	function __construct() {
		parent::__construct();
		
		//connect to db, if not in demo index page;
		if (rpd::$uri_string != "demo")
			rpd::connect ();
	}
	function index()
	{
		$data['title'] 		= 'Repyd Demos';
		$data['content']	= nl2br(rpd::view('home'));
		$data['code'] 		= '';

		//output
		echo rpd::view('demo', $data);
	}

	function menu()
	{
		return rpd::view("demo_menu");
	}
	

}
