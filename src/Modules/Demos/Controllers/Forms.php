<?php

namespace Modules\Demos\Controllers;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use \Modules\Demos\Models\Article;

class Forms extends \Rapyd\Controller
{

    public function indexAction()
    {
        //fill form data using a model
        $article = Article::find(1);

        
        $form = $this->form->createBuilder();
        $form->add('title', 'text', 
                array('constraints' => array( new NotBlank(), new Length(array('min'=>4))),
                      'attr' => array('placeholder'=>'article title'))
         );
         $form->add('body', 'textarea', array('constraints' => array(new NotBlank())));
         $form->add('public', 'checkbox', array('required' => false));
         $form->add('save', 'submit');
         $form->setData($article->attributesToArray());
         $form = $form->getForm();

        //there is a post?
        if ($this->app->request()->post($form->getName())) {
            
            $form->bind($this->app->request()->post($form->getName()));

            if ($form->isValid()) {
                //bind model with new values and save
                $article->setRawAttributes($form->getData());
                $article->save();
            }
        }

        $this->render('Form', array('testform' => $form->createView()));
    }
}