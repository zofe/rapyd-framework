<?php

namespace Modules\Demos\Controllers;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use \Modules\Demos\Models\Article;

class Eloquent extends \Rapyd\Controller
{

    public function indexAction()
    {
        //get first article using an eloquent model
        $article = Article::with("author")->find(1);

        $this->render('Eloquent', array('article' => $article));
    }
}