<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function MenuTopAction()
    {
        $modules = $this->getDoctrine()
            ->getRepository('ExpressoBundle:Module')
            ->findALL();

        $menu = array();
        //Todo: Validar Permisão
        foreach ( $modules as $module )
            if($module->getActive())
                $menu[$module->getOrder()] = array( 'title' => $this->get('translator')->trans($module->getTitle()) , 'name' => $module->getName());

        $response = new Response(json_encode($menu));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function MenuAdminAction()
    {
        $adminGroups = $this->getDoctrine()
            ->getRepository('ExpressoBundle:AdminMenuGroup')
            ->findALL();

        $menu = array();

        foreach ( $adminGroups as $adminGroup )
        {
            $tmp = array();
            $tmp['title'] =  $this->get('translator')->trans($adminGroup->getTitle());
            $tmp['name'] = $adminGroup->getName();
            $tmp['itens'] = array();

            $adminItens = $this->getDoctrine()
                ->getRepository('ExpressoBundle:AdminMenuItem')
                ->findBy(array('adminMenuGroup' => $adminGroup ));

            foreach($adminItens as $adminIten )
            {
                $tmp2 = array();
                $tmp2['name'] = $adminIten->getName();
                $tmp2['title'] = $this->get('translator')->trans($adminIten->getTitle());
                $tmp['itens'][] = $tmp2;
            }

            $menu[] = $tmp;

        }

        $response = new Response( json_encode($menu) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
