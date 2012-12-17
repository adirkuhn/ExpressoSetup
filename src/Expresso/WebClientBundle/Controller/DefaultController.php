<?php

namespace Expresso\WebClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction($module = '')
    {
        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $parameters = $configurator->getParameters();

        if($parameters['setup'] == 1) {
            $modules[] = array("name" => "Some module","description" => "Alguma descrição", "url" => "expresso.livre/module.Foo");

            //php composer.phar search monolog
            //
            //$teste = shell_exec("php composer.phar search -N expresso.livre");
            /*ob_start();
            print_r( "=== LOG BEGIN ===" . "\n" );
            print_r(     $teste        );
            print_r( "=== LOG END ===" . "\n" );
            $output = ob_get_clean();
            file_put_contents( '/tmp/log-gustavo2.txt',  $output );*/

            return $this->render('SetupBundle:Setup:modules.html.twig',array(
                'modules' => $modules
            ));
        }

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
        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $parameters = $configurator->getParameters();


        return $this->render('WebClientBundle:Default:admin.html.twig',array(
            'cn' => $this->getUser()->getAttribute('cn'),
            'mail' => $this->getUser()->getAttribute('mail')
        ));
    }

    public function loginAction()
    {
        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $parameters = $configurator->getParameters();

        if($parameters['setup'] == 2) {
            return $this->redirect($this->generateUrl('ExpressoSetupBundle_homepage'), 301);
        }

        //return 
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

