<?php

namespace Modules\Demos\Controllers;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class Forms extends \Rapyd\Controller
{

    public function indexAction()
    {
        
        // Create our first form!
        $form = $this->form->createBuilder()
            ->add('firstName', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min'=>4)),
                ),
            ))
            ->add('lastName', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min'=>4)),
                ),
            ))
            ->add('gender', 'choice', array(
                'choices' => array('m' => 'Male', 'f' => 'Female'),
            ))
            ->add('newsletter', 'checkbox', array(
                'required' => false,
            ))
            ->getForm();

        if (isset($_POST[$form->getName()])) {
            $form->bind($_POST[$form->getName()]);

            if ($form->isValid()) {
                var_dump('VALID', $form->getData());
                die;
            }
        }

        $data['title'] = 'Form Controller';
        $data['active'] = 'forms';
        $data['content_raw'] = $this->fetch('Form', array('form' => $form->createView()));
        $data['form'] = $form->createView();
        $data['code'] = highlight_string(file_get_contents(__FILE__), TRUE);

        $this->render('Demo', $data);
       
    }
}