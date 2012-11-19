<?php


class edit_controller extends demo_controller {


	function index()
	{
		//edit
		$edit = new dataedit_library();
		$edit->label = 'Manage Article';
		$edit->back_url = rpd::url('filtered_grid/index');

		$edit->source('demo_articles');
		$edit->field('input','title','Title')->rule('trim|required');
		$edit->field('radiogroup','public','Public')->options(array("y"=>"Yes", "n"=>"No"));
		$edit->field('dropdown','author_id','Author')->options('SELECT author_id, firstname FROM demo_authors')
			 ->rule('required');
		$edit->field('date','date','Date')->attributes(array('style'=>'width: 100px'));
		$edit->field('editor','body','Description')->attributes(array('style'=>'width: 300px'))->rule('required');

		$edit->buttons('modify','save','undo','back');

		$edit->build();

		//$data['head']	= $this->head();
		$data['title'] 	= 'DataEdit';
		$data['content']= $edit.'<br />';
		$data['code'] 	= file_get_contents(__FILE__);

		//output
		echo rpd::view('demo', $data);
	}


}
