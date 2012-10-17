<?php

class edit_grid_controller extends demo_controller {

	public function article()
	{
		//article dataedit
		$edit = new dataedit_library();
		$edit->label = 'Manage Articls';
		$edit->source('demo_articles');
		$edit->field('input','title','Title')->rule('trim|required');
		$edit->field('rteditor','body','Description')->rule('required');
		$edit->field('container','comments','m');
		$edit->buttons('modify','save','undo');
		$edit->build();

		if ($this->uri->value('show'))
		{
			if ($this->uri->value('show1|modify1|update1|create1|insert1|do_delete1'))
			{
				$edit->nest('comments',$this->comment());
			} else {
				$edit->nest('comments',$this->comments());
			}
		}
		$data['head']	= $this->head();
		$data['title']	= 'DataEdit + DataGrid + Dataedit (Master-Detail)';
		$data['content']= '<em>full crud in 70 lines of code</em><br />'.$edit->output;
		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}

	public function comments()
	{
		//comments datagrid configuration
		$grid = new datagrid_library();
		$grid->label = 'Comments';
		$grid->source('demo_comments');
		$grid->db->where('article_id',$this->uri->value('create|show|modify|update'));
		$grid->db->orderby('comment_id','desc');
		$grid->column('comment_id', 'ID', true)
				->url('edit_grid/article/show/{article_id}/modify1/{comment_id}','detail.gif');
		$grid->column('comment','Comment');
		$grid->column('delete', 'delete')
				->url('edit_grid/article/show/{article_id}/do_delete1/{comment_id}');
		$grid->buttons('add');
		$grid->build();

		return $grid->output;
	}

	public function comment()
	{
		//comments dataedit
		$edit = new dataedit_library();
		$edit->label = 'Manage Comment';
		$edit->back_url = $this->url('edit_grid/article/show/'.$this->uri->value('show'));
		$edit->back_save = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save = true;
		$edit->source('demo_comments');
		$edit->field('hidden','article_id','')->insert_value($this->uri->value('show'));
		$edit->field('textarea','comment','Comment');
		$edit->field('captcha','captcha','retype code');
		$edit->buttons('modify','save','undo','back');
		$edit->build();

		return $edit->output;
	}

}
