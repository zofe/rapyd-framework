<?php


class demo_controller extends controller_controller {


	function index()
	{
		//$data['head']		= $this->head();
		$data['title'] 		= 'Repyd Demos';
		$data['content']	= nl2br(rpd::view('meme'));
		$data['code'] 		= '';

		//output
		echo rpd::view('demo', $data);
	}

	function menu()
	{
		return rpd::view("demo_menu");
	}
}
