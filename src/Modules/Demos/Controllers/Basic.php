<?php

namespace Modules\Demos\Controllers;

class Basic extends \Rapyd\Controller
{

    public function indexAction()
    {
        $data['title'] = 'Basic Controller';
        $data['active'] = 'basic';
        $data['content'] = 'Hello World!';
        $data['code'] = highlight_string(file_get_contents(__FILE__), TRUE);

        $this->render('Demo', $data);
    }

}