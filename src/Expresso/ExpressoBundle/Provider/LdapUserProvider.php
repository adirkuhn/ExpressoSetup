<?php

namespace Expresso\ExpressoBundle\Provider;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    Expresso\ExpressoBundle\User\LdapUser,
    Expresso\ExpressoBundle\Service\LdapService;

class LdapUserProvider implements UserProviderInterface
{

  protected $ldapService;

  public function __construct(LdapService $ldapService)
  {
    $this->ldapService = $ldapService;
  }
  
  public function loadUserByUsername( $username )
  {
    $this->ldapService->connect();
    $this->ldapService->bindAdmin();
    $u = $this->ldapService->getUserByUid($username);

    if(!$u)
      throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
       
    $ldapUser = new LdapUser();
    $ldapUser->setAttributes($u);

      $ldapUser->setAttribute('groups' , $this->ldapService->getGroupsByUid($username , array('cn','gidNumber','description','uid')) );


    $roles = array();
    foreach ($this->ldapService->getRulesByUid($username , array('cn')) as $key => $value)
      $roles[] = 'ROLE_'.strtoupper($value['cn']);
    
    $ldapUser->setRoles($roles);

    return $ldapUser;
  }

  public function refreshUser(UserInterface $user)
  {
    if(!$user instanceof LdapUser)
      throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
    
    //TODO: remover isto o quanto antes
    //Carrega dados do expresso antigo
    //$this->expressoVelho();

    return $this->loadUserByUsername($user->getUsername());
  }

    public function expressoVelho()
    {
        return 1;
    }

  public function supportsClass($class)
  {
    return (bool) ($class === 'Expresso\LdapBundle\User\LdapUser');
  }
}
