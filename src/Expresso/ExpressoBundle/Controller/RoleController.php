<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RoleController extends Controller
{

    public function CreateAction()
    {
        $request = $this->get('request');
        $ldap = $this->get('ExpressoLdap');

        if(!$request->request->get('cn'))
            throw new \Exception('cn can not be null');

        $members = (is_array($request->request->get('members')) && count($request->request->get('members')) > 0  ) ? $request->request->get('members') : array();

        $ldap->creatRule($request->request->get('cn') , $members , $request->request->get('description') );
    }
}
