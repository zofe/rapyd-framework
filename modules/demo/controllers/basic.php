<?php


class basic_controller extends demo_controller {

	function hello()
	{
		//fill an array with some data..
		$data['content']= 'Hello World! <br />';
		$data['code']	= file_get_contents(__FILE__);

		//then echo/print a view
		echo rpd::view('demo', $data);

	}

}


