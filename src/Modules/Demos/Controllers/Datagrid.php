<?php

namespace Modules\Demos\Controllers;

use Modules\Demos\Models\Article;

class Datagrid extends \Rapyd\Controller
{

    public function indexAction()
    {

        //dataset widget
        $dg = $this->grid->createBuilder();
        $dg->setSource(Article::with("author"));
        $dg->setPagination(10);
        $dg->add('article_id',"ID", true);
        $dg->add('title',"title");
        $dg->add('<em>{{ article.author.firstname|lower }}</em>',"author");
        $dg->getGrid();

        $this->render('Grid', array('dg' => $dg));
    }

}
