<?php

namespace Modules\Demos\Controllers;

class Hello extends \Rapyd\Controller
{

    public function indexAction()
    {
        $this->render('Hello', array('somevar'=>'Hello World!'));
    }

}
