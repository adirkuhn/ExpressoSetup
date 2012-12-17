<?php

namespace Expresso\ExpressoBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Expresso\ExpressoBundle\ConfigStep\DBStep;
use Expresso\ExpressoBundle\ConfigStep\LdapStep;
use Expresso\ExpressoBundle\ConfigStep\EmailStep;

class ExpressoBundle extends Bundle
{
    public function boot()
    {
        if ( $this->container->get('kernel')->getEnvironment() === 'dev' ) {
            //getting configurator service and parameters
            $configurator = $this->container->get('sensio.distribution.webconfigurator');
            $parameters = $configurator->getParameters();
        }
    }
}
