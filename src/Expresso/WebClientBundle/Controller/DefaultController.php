<?php

namespace Expresso\WebClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction($module = '')
    {
        if(!$module || !file_exists(__DIR__.'/../Resources/public/js/Module/'.$module.'.js')) // Caso o Module não exista carregar o home
            $module = 'Home';

        return $this->render('WebClientBundle:Default:index.html.twig',array(
            'cn' => $this->getUser()->getAttribute('cn'),
            'mail'         => $this->getUser()->getAttribute('mail'),
            'loadModule' => $module
        ));

    }

    public function AdminAction()
    {
        return $this->render('WebClientBundle:Default:admin.html.twig',array(
            'cn' => $this->getUser()->getAttribute('cn'),
            'mail' => $this->getUser()->getAttribute('mail')
        ));
    }

    public function loginAction()
    {
       	$request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('WebClientBundle:Default:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
}
