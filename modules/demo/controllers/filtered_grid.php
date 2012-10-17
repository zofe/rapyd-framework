<?php


class filtered_grid_controller extends demo_controller {


	function index()
	{
		//filter

		$filter = new datafilter_library();
		$filter->label = 'Article Filter';
		$filter->db->select("art.*, aut.*");
		$filter->db->from("demo_articles art");
		$filter->db->join("demo_authors aut","aut.author_id=art.author_id","LEFT");
		$filter->field('input','title','Title')->attributes(array('style' => 'width:170px'));
		$filter->field('radiogroup','public','Public')->options(array("y"=>"Yes", "n"=>"No"));
		$filter->buttons('reset','search');
		$filter->build();

		$grid = new datagrid_library();
		$grid->label = 'Article List';
		$grid->per_page = 5;
		$grid->cid = '';
		$grid->source($filter);
		$grid->column('article_id','ID',true)->url('edit/index/show/{article_id}','detail.gif');
		$grid->column('title','Title',true);
		$grid->column('body','Body')->callback('escape',$this);
		$grid->column('{firstname} {lastname}','Author');
		$grid->build();

		$data['head']	= $this->head();
		$data['title']	= 'DataGrid+DataFilter';
		$data['content']= $filter.'<br />'.$grid.'<br />';
		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}

	function escape($row)
	{
		return htmlspecialchars(substr($row['body'],0,10));
	}

}



