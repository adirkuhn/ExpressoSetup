<?php

namespace Expresso\ExpressoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
    public function CreateFindUserAction()
    {
        $request = Request::createFromGlobals();
        $response = new Response();
        $find =  $this->get('ExpressoFind');

        $data = array();
        $data['find'] = $request->request->get('find');
        $data['fields'] = $request->request->get('fields');

        if($location = $find->create('/rest/Expresso/FindUser' , $data )){
            $response->headers->set('Content-Location', $location );
            $response->setStatusCode(201);
        }else{
            //$response->setContent($this->get('translator')->trans(  ));
            $response->setStatusCode(400);
        }
        return $response;
    }

    public function GetFindUserAction( $find )
    {
        $find =  $this->get('ExpressoFind');
        if( $data =  $find->get( $find ) ) //Caso a busca ainda exista
        {
            $ldap = $this->get('ExpressoLdap');
            $filter = '(&'.$ldap->getFilter('user') . $ldap->stemFilter($data['find'], array('cn', 'givenName', 'uid', 'sn', 'displayName', 'mail', 'mailAlternateAddress') , '|').')';
            return new Response(json_encode($ldap->search( $filter ,  (is_array($data['fields'])) ? $data['fields'] : array() , $ldap->getConfig('userContext') )));
        }
        else //Retorna Not Found
        {
            $response = new Response();
            $response->setStatusCode(404);
            return $response;
        }
    }

    public function jqGridListUsersAction( $filter = false )
    {
        $request = Request::createFromGlobals();
        $ldap = $this->get('ExpressoLdap');
        $filter = $filter ? '(&'.$ldap->getFilter('user') . $ldap->stemFilter(base64_decode($filter), array('cn', 'givenName', 'uid', 'sn', 'displayName', 'mail', 'mailAlternateAddress') , '|').')' : $ldap->getFilter('user');
        $limit= $request->query->get('rows') ? (int)$request->query->get('rows') : 25;
        $offset= $request->query->get('page') ? ((int)$request->query->get('page')*$limit - $limit): 0;
        $sort= $request->query->get('sidx') ? $request->query->get('sidx') : 'cn';

        $return = array();
        $return['records'] = $ldap->count( $filter) ;
        $return['total'] = $return['records'] > 0 ? ceil( $return['records']/$limit ) : 0;
        $return['page'] = (int)$request->query->get('page') ? (int)$request->query->get('page') : 1;
        $return['rows'] = $ldap->search( $filter  , array('uid','cn','mail') , false , $sort , $limit , $offset);

        $response = new Response( json_encode( $return ) );
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    public function CreateUserAction()
    {
        $request = Request::createFromGlobals();
        $ldap = $this->get('ExpressoLdap');
        $imap = $this->get('ExpressoImap');

        $att['objectClass'][] = 'posixAccount';
        $att['objectClass'][] = 'inetOrgPerson';
        $att['objectClass'][] = 'shadowAccount';
        $att['objectClass'][] = 'qmailUser';
        $att['objectClass'][] = 'phpgwAccount';
        $att['objectClass'][] = 'top';
        $att['objectClass'][] = 'person';
        $att['objectClass'][] = 'organizationalPerson';

        $att['phpgwAccountType'] = 'u';
        $att['phpgwAccountExpires'] = '-1';


        $att['phpgwaccountstatus'] = $request->request->get('') ? 'A' : 'D';
        $att['accountStatus'] = $request->request->get('') ? 'active' : 'desatived';

        $att['uid'] = $request->request->get('uid');
        $att['uidNumber'] = $request->request->get('registration');


        $att['l'] = $request->request->get('bairro'); //Bairro


        $att['cn'] = $request->request->get('cn');
        $att['sn'] = $request->request->get('sn'); //Ultimo Nome


        $att['password'] = $request->request->get('password') ;

        $att['city'] = $request->request->get('city');//Cidade
        $att['street'] = $request->request->get('street'); //Rua
        $att['st'] = $request->request->get('st');//Estado
        $att['telephonenumber'] = $request->request->get('telephone');
        $att['mail'] =  $request->request->get('mail');


        //GT SR PV
        $att['o'][] = $request->request->get('pv');
        $att['o'][] = $request->request->get('sr');
        $att['o'][] = $request->request->get('gitec');

        $att['employeenumber'] = $request->request->get('');//Matricula
        $att['cpf'] = $request->request->get('');
        $att['rg'] = $request->request->get('');
        $att['rguf'] = $request->request->get('');
        $att['description'] = $request->request->get('');

        $result['mail']					= $request->request->get('mail');
        $result['mailalternateaddress']	= $request->request->get('alternate-mail');
        $result['mailforwardingaddress']= $request->request->get('forwarding-mail');

        $att[''] = $request->request->get('');

        $att[''] = $request->request->get('');

        $dn = 'uid='.$att['uid'].',' . $ldap->getConfig('userContext');


        $imap->createAccount($att['uid'] , $request->request->get(''));

    }



}
