<?php

class app_controller extends controller_controller
{

	public function head()
	{
		return rpd::head();
	}
	
	
	public function header()
	{
		return rpd::view('header');
	}

	public function footer()
	{
		return rpd::view('footer');
	}

}
