<?php


class hmvc_controller extends demo_controller {

	function __construct()
	{
		$this->article = new article_model();
		
	}
	
	function index()
	{
		//do db stuff..
		$articles = $this->article->get_articles();

		
		$data['title']	= 'HMVC';
		$data['content']= $this->view('hmvc',array('articles'=>$articles));

		$data['code']	= file_get_contents(__FILE__)
		."\n\nMODEL\n\n".file_get_contents(dirname(__FILE__).'/../models/article.php')
		."\n\nVIEW\n\n".file_get_contents(dirname(__FILE__).'/../views/hmvc.php');

		//call main view
		echo rpd::view('demo', $data);
	}
	

	function header()
	{
		//do db stuffs then call a view
		$count = $this->article->count_articles();
		return rpd::view('mvc_header', array('count'=>$count));
	}
	
	function footer()
	{
		//do db stuffs then call a view
		return rpd::view('mvc_footer');
	}

}
