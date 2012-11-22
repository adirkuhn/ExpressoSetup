<?php

namespace Expresso\SetupBundle\ConfigStep;

use Sensio\Bundle\DistributionBundle\Configurator\Step\DoctrineStep;
use Expresso\SetupBundle\Form\DbFormType;

class DBStep extends DoctrineStep
{

    /**
     * @see StepInterface
     */
    public function getFormType()
    {
        return new DbFormType();
    }

    /**
    *
    */
    public function getTemplate()
    {
        return 'SetupBundle:Setup:db.html.twig';
    }
}