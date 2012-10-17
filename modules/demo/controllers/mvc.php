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
		$data['content']= $this->view('mvc',array('articles'=>$articles, 'count'=>$count));

		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE)
	."<br>MODEL<br>".highlight_string(file_get_contents(dirname(__FILE__).'/../models/article.php'), TRUE)
	."<br>VIEW<br>".highlight_string(file_get_contents(dirname(__FILE__).'/../views/mvc.php'), TRUE);

		//call main view
		echo $this->view('demo', $data);
	}


}
