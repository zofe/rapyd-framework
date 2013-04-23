<?php


class mvc_controller extends demo_controller {

	function __construct()
	{
		$this->article = new article_model();
		
	}
	
	function index()
	{
		//do db stuff..
		$articles = $this->article->get_articles();
		$count = $this->article->count_articles();
		

		$data['title']	= 'MVC';
		$data['content']= rpd::view('mvc',array('articles'=>$articles, 'count'=>$count));

		$data['code']	= file_get_contents(__FILE__)
		."\n\nMODEL\n\n".file_get_contents(dirname(__FILE__).'/../models/article.php')
		."\n\nVIEW\n\n".file_get_contents(dirname(__FILE__).'/../views/mvc.php');

		//call main view
		echo rpd::view('demo', $data);
	}


}
