<?php


class basic_controller extends demo_controller {

	function hello()
	{
		//fill an array with some data..
		$data['content']= 'Hello World! <br />';
		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//then echo/print a view
		echo $this->view('demo', $data);

	}

}


