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

            $configurator->addStep(new DBStep($parameters));
            $configurator->addStep(new LdapStep($parameters));
        }
    }
}
