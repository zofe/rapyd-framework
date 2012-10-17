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

		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE)
	."<br>MODEL<br>".highlight_string(file_get_contents(dirname(__FILE__).'/../models/article.php'), TRUE)
	."<br>VIEW<br>".highlight_string(file_get_contents(dirname(__FILE__).'/../views/hmvc.php'), TRUE);

		//call main view
		echo $this->view('demo', $data);
	}
	

	function header()
	{
		//do db stuffs then call a view
		$count = $this->article->count_articles();
		return $this->view('mvc_header', array('count'=>$count));
	}
	
	function footer()
	{
		//do db stuffs then call a view
		return $this->view('mvc_footer');
	}

}
