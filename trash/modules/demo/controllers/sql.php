<?php


class sql_controller extends demo_controller {

	function simple_query()
	{
		$this->db->query('select * from demo_articles');
		$articles =  $this->db->result_array();
		
		$data['title']	= 'Simple SQL';
		$data['content']= $this->view('article_list',array('articles'=>$articles));

		$data['code']	= file_get_contents(__FILE__)."\n VIEW\n".
						  file_get_contents(dirname(__FILE__).'/../views/article_list.php');

		//output
		echo $this->view('demo', $data);
	}
	
	
	function query_builder()
	{
		$this->db->select('*')->from('demo_articles')->where('article_id', 1)->get();
		
		$article_one =  $this->db->row_object();
		
		$data['title']	= 'Query Builder';
		$data['content']= 'first article title is: '.$article_one->title;

		$data['code']	= file_get_contents(__FILE__);

		//output
		echo rpd::view('demo', $data);
	}
}


