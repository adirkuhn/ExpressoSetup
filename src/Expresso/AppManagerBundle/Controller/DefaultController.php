<?php

namespace Expresso\AppManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('AppManagerBundle:Default:index.html.twig', array('name' => 'something'));
    }
}
