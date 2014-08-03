<?php

namespace Modules\Demos\Controllers;

use Modules\Demos\Models\Article;

class Dataset extends \Rapyd\Controller
{

    public function indexAction()
    {

        $ds = $this->set->createBuilder();
        $ds->setSource(Article::with("comments", "author"));
        $ds->setPagination(5);
        $ds->getSet();

        $this->render('Set', array('ds' => $ds));
    }

}
