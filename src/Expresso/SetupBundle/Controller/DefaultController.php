<?php

namespace Expresso\SetupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Config\Definition\Exception\Exception;

class DefaultController extends Controller
{

    /**
    * Telas de cnfiguração básica do Expresso
    */
    //TODO: traduzir mensagens em inglês
    //TODO: aplicar layout nas telas
    public function setupAction($index = 0)
    {
        if (!$this->checkDevEnvironment()) {
            return $this->container->get('templating')->renderResponse('SetupBundle:Setup:deny.html.twig');
        }

        $configurator = $this->container->get('sensio.distribution.webconfigurator');

        //Como este seviço de configuração é do symfony, ele adiciona suas configurações
        //de bando de dados e secret, então é necessário remover eles do valor do count
        if($configurator->isFileWritable()){
            $stepCount = $configurator->getStepCount() - 2;

            $step = $configurator->getStep($index);

            $form = $this->container->get('form.factory')->create($step->getFormType(), $step);

            $request = $this->container->get('request');
            
            if ('POST' === $request->getMethod()){
                $form->bindRequest($request);
                if ($form->isValid()) {
                    $oi = $step->update($form->getData());
                    $configurator->mergeParameters($oi);
                    $configurator->clean();
                    $configurator->write();
                    if(array_key_exists ('dn', $step)){
                        try{
                            $ads = $this->get("ExpressoLdap");
                            $ads->connect();
                        }catch (\Exception $e){
                            return new RedirectResponse($this->container->get('router')->generate('ExpressoSetupBundle_setup', array('index' => $index, 'error' => $e->getMessage())));
                        }
                    }

                    $index++;

                    if ($index < $stepCount) {
                        return new RedirectResponse($this->container->get('router')->generate('ExpressoSetupBundle_setup', array('index' => $index)));
                    }

                    return new RedirectResponse($this->container->get('router')->generate('ExpressoSetupBundle_final'));
                }
            }

            //$index++;

            return $this->container->get('templating')->renderResponse($step->getTemplate(), array(
                'form'    => $form->createView(),
                'index'   => $index,
                'count'   => $stepCount,
                'version' => $this->getVersion(),
            ));
        }else{
            /*ob_start();
            print_r( "=== LOG BEGIN ===" . "\n" );
            print_r(     $this->container->get('router')->generate('ExpressoSetupBundle_final')         );
            print_r( "=== LOG END ===" . "\n" );
            $output = ob_get_clean();
            file_put_contents( '/tmp/log-gustavo2.txt',  $output );*/
            return new RedirectResponse($this->container->get('router')->generate('ExpressoSetupBundle_check'));
        }
    }

    public function finalAction()
    {
        if (!$this->checkDevEnvironment()) {
            return $this->container->get('templating')->renderResponse('SetupBundle:Setup:deny.html.twig');
        }

        $configurator = $this->container->get('sensio.distribution.webconfigurator');
        $oi['setup'] = 1;
        $configurator->mergeParameters($oi);
        $configurator->clean();
        $configurator->write();

        try {
            $welcomeUrl = $this->container->get('router')->generate('_welcome');
        } catch (\Exception $e) {
            $welcomeUrl = null;
        }

        return $this->container->get('templating')->renderResponse('SetupBundle:Setup:final.html.twig', array(
            'welcome_url' => $welcomeUrl,
            'parameters'  => $configurator->render(),
            'yml_path'    => $this->container->getParameter('kernel.root_dir').'/config/parameters.yml',
            'is_writable' => $configurator->isFileWritable(),
            'version'     => $this->getVersion(),
        ));
    }

    /**
    * Verifica se esta no ambiente de desenvolvimento
    * @return <boolean>
    */
    public function checkDevEnvironment()
    {
        return ( $this->container->get('kernel')->getEnvironment() === 'dev' );
    }

    public function checkEnviromentAction()
    {
        
        return $this->container->get('templating')->renderResponse('SetupBundle:Setup:check.html.twig', array(
        ));
    }

    public function getVersion()
    {
        $kernel = $this->container->get('kernel');

        return $kernel::VERSION;
    }
}
