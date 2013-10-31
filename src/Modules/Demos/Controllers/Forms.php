<?php

namespace Modules\Demos\Controllers;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use \Modules\Demos\Models\Article;

class Forms extends \Rapyd\Controller
{

    public function indexAction()
    {
        
        $article = Article::find(1);

        
        $form = $this->form->createBuilder()
            ->add('title', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min'=>4)),
                ),
                'attr' => array('placeholder'=>'article title'),
            ))
            ->add('body', 'textarea', array(
                'constraints' => array(
                    new NotBlank(),
                ),
            ))
            ->add('public', 'checkbox', array(
                'required' => false,

            ))
            ->add('save', 'submit')
            ->setData($article->attributesToArray())
            ->getForm();

        if (isset($_POST[$form->getName()])) {
            $form->bind($_POST[$form->getName()]);

            
            if ($form->isValid()) {

                $article->setRawAttributes($form->getData());
                $article->save();
            }
        }

        $this->render('Form', array('testform' => $form->createView()));
    }
}