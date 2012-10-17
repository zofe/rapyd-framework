<?php


class form_controller extends demo_controller {


	function index()
	{

		//form

		$form = new dataform_library();
		$form->validation->set_message('customcheck','Error, field "name" must be filled with "xxx"');
		$form->field('input','name','Name')
			->rule('required|trim|callback_customcheck')
			->group('personal data')
			->attributes(array('style'=>'width: 100px'));

		$form->field('input','lastname','Lastname')
			->rule('trim','required')
			->group('fiscal data')
			->in('name');

		$form->field('input','cod_fiscale','vat code')
			->rule('required','exact_length[16]')
			->mask('aaaaaa99a99a999a')
			->group('fiscal data')
			->rule('required');

		//$form->buttons(array ('save' => 'save|Next Step'));
		$form->buttons('save');
		$form->build();

		//flow control
		if ($form->on('show') OR $form->on('error'))
		{
			$output = $form->output;
		}

		if ($form->on('success'))
		{
			var_dump($_POST);
		}

		$data['head']	= $this->head();
		$data['title']	= 'DataForm';
		$data['content']= $form.'<br />';
		$data['code']	= highlight_string(file_get_contents(__FILE__), TRUE);

		//output
		echo $this->view('demo', $data);
	}
	
	function customcheck($value)
	{
		if ($value!='xxx')
			return false;

		return true;
	}


}
