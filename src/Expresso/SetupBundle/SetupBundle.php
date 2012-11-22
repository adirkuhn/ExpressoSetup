<?php

namespace Expresso\SetupBundle;

use Expresso\SetupBundle\ConfigStep\DBStep;
use Expresso\SetupBundle\ConfigStep\LdapStep;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SetupBundle extends Bundle
{

	public function boot()
    {
        if ( $this->container->get('kernel')->getEnvironment() === 'dev' ) {
            //getting configurator service and parameters
            $configurator = $this->container->get('sensio.distribution.webconfigurator');
            $parameters = $configurator->getParameters();

            //check if the config file (parameters.ini) has all keys
            //TODO: needs to implements individual server check and 'connection' test
           
            //create config steps
            $configurator->addStep(new DBStep($parameters));
            $configurator->addStep(new LdapStep($parameters));
            //Quando houver necessidade será reativado
            //$configurator->addStep(new EmailStep($parameters));
        }
    }
}
