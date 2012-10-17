<?php


class upload_controller extends demo_controller {


	public function file()
	{
		//form
		$form = new dataform_library();
		$form->label = 'Manage Files';
		$form->back_url = $this->url('upload/show');

		$form->field('upload','filename','Image')
			->upload_path(DOC_ROOT.'public/uploads/')
			->allowed_types('jpg|gif')
			->max_size('2M');
		$form->button('back','List','window.location.href=\''.$this->url('upload/show').'\'','TR');
		$form->buttons('save');
		$form->build();

		if ($form->on('show') OR $form->on('error'))
		{
			$output = $form->output;
		}

		if ($form->on('success'))
		{
			$output  = 'thnks'."<br />";
			$output .= '<a href="'.$this->url('upload/show').'">List</a>';
		}

		$data['head']	= $this->head();
		$data['title'] 	= 'Upload';
		$data['content']= $output.'<br />';
		$data['code'] 	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}

	public function show()
	{
		//list all files in uploads dir
		$files = array();
		$i = 0;
		$handler = opendir(DOC_ROOT.'uploads');
		while ($file = readdir($handler)) {
			if ($file[0]!='.'){
				$i++;
				$files[] = array('file_id'=>$i,
								 'filename'=>$file,
								 'filepath'=>DOC_ROOT.'public/uploads/'.$file);
			}
		}
		closedir($handler);

		//datagrid array driven
		$grid = new datagrid_library();
		$grid->label = 'Uploads';
		$grid->per_page = 10;
		$grid->source($files);
		$grid->column('file_id', 'ID', true)->attributes(array('style' => 'width:170px'));
		$grid->column('<a href="{filepath}">{filename}</a>', 'File');
		$grid->button('add','upload','window.location.href=\''.$this->url('upload/file').'\'','TR');
		$grid->build();

		$data['head']	= $this->head();
		$data['title'] 	= 'Upload';
		$data['content']= $grid.'<br />';
		$data['code'] 	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}

}
