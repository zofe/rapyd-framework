<?php

namespace Modules\Demos\Controllers;

class Demo extends \Rapyd\Controller
{

    public function indexAction()
    {
        $this->render('Index');
    }

}