<?php

namespace Expresso\ExpressoBundle\Service;

//use Monolog\Logger;

class LdapService
{
    private $config = array();
    private $filters = array();
    private $_ress;
    //private $log;

    public function __construct(array $config , array $filters)
    {
        $this->config = $config;

        $this->filters = $filters;

        /**
         * @todo validar campos
         */

        $this->connect();
    }

    /**
     * Função para buscas 
     * 
     * @license    http://www.gnu.org/copyleft/gpl.html GPL 
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br) 
     * @sponsor    Caixa Econômica Federal 
     * @author     Cristiano Corrêa Schmidt 
     * @param      int $filter filtro
     * @param      int $attibutes atributos a serem retornados
     * @return     array 
     * @access     public 
     */
    public function search( $filter , array $attibutes = array() , $dn = false , $sort =  false , $limit = false , $offset = false )
    {       
        $dn = ($dn) ? $dn : $this->config['base_dn'];

        $searchLimit = ($limit !== false && $offset === false) ? $limit : ($limit === false && $offset === false) ? $this->config['sizelimit'] :  0;

        $search = ldap_search($this->_ress, $dn, $filter, $attibutes , 0 , $searchLimit );
        if($sort)ldap_sort($this->_ress, $search, $sort);

        if($offset !== false )
        {
            $return = array();
            $iTotal = ldap_count_entries( $this->_ress, $search );
            if($iTotal < 1 ) return false;
            $rEntry = ldap_first_entry(  $this->_ress, $search );

            $start = $offset;
            $end = $start + $limit;

            for ( $iCurrent = 0; $iCurrent < $iTotal ;$iCurrent++)
            {
                if($iCurrent < $start)
                {
                    $rEntry = ldap_next_entry( $this->_ress, $rEntry );
                    continue;
                }
                if( $iCurrent >= $end)  break;

                $return[] =  $this->formatEntries(ldap_get_attributes( $this->_ress, $rEntry )) ;
                $rEntry = ldap_next_entry( $this->_ress, $rEntry );

            }

            return $return;
        }

        return $search ? $this->formatEntries(ldap_get_entries($this->_ress, $search)) : false;
    }


    public function count( $filter , $dn = false)
    {
        $dn = ($dn) ? $dn : $this->config['base_dn'];

        $search = ldap_search($this->_ress, $dn, $filter, array() , 0 , 0);
        return $search ? ldap_count_entries( $this->_ress , $search) : false;
    }

    public function getUserByUid( $uid , array $atributes = array() )
    {       
      $s =  $this->search('(&(uid='.$this->escape($uid).')'.$this->filters['user'].')' , $atributes );
      return isset($s[0]) ? $s[0] : null;
    }
    
    public function getGroupsByUid( $uid , array $atributes = array())
    {       
      return  $this->search('(&(memberUid='.$this->escape($uid).')'.$this->filters['group'].')' , $atributes );
    }

    public function bind($userDn, $password)
    {
        if (!$userDn) 
            throw new \Exception('You must bind with an ldap userDn');
        
        if (!$password) 
            throw new \Exception('Password can not be null to bind'); 

        return (bool)ldap_bind($this->_ress, $userDn, $password);
    }

    public function bindAdmin()
    {
        if (!$this->config['adminname']) 
            throw new \Exception('You must bind with an ldap userDn (adminname)');
        
        if (!$this->config['adminpass'])
            throw new \Exception('Password can not be null to bind (adminpass)'); 

        return $this->bind($this->config['adminname'] , $this->config['adminpass'] );
    }

    public function bindUser()
    {
        if (!$this->config['username']) 
            throw new \Exception('You must bind with an ldap userDn (adminname)');
        
        if (!$this->config['userpass'])
            throw new \Exception('Password can not be null to bind (adminpass)'); 

        return $this->bind($this->config['username'] , $this->config['userpass'] );
    }
   
    private function connect()
    {
        $port = isset($this->config['port']) ? $this->config['port'] : '389' ;
        $ress = ldap_connect($this->config['host'], $port);

        if (isset($this->config['version']) && $this->config['version'] !== null) 
            ldap_set_option($ress, LDAP_OPT_PROTOCOL_VERSION, $this->config['version']);
            
        if (isset($this->config['referrals_enabled']) && $this->config['referrals_enabled'] !== null) 
            ldap_set_option($ress, LDAP_OPT_REFERRALS, $this->config['referrals_enabled']);

        if (isset($this->config['username']) && $this->config['version'] !== null) 
        {
            if(!isset($this->config['userpass'])) 
                throw new \Exception('You must uncomment password key');
            
            $bindress = ldap_bind($ress, $this->config['username'], $this->config['userpass']);
        
            if (!$bindress) 
                throw new \Exception('The credentials you have configured are not valid');
        } 
        else 
        {
            $bindress = ldap_bind($ress);
        
            if (!$bindress) 
                throw new \Exception('Unable to connect Ldap');
        }
    
        $this->_ress = $ress;
        return $this->_ress;
    }

    /**
     * Retorna o endereço de e-mail da conta pelo uidNumber 
     * 
     * @license    http://www.gnu.org/copyleft/gpl.html GPL 
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br) 
     * @sponsor    Caixa Econômica Federal 
     * @author     Cristiano Corrêa Schmidt 
     * @param      int $uidNumber uidNumber da conta 
     * @return     string 
     * @access     public 
     */
    public function getMailByUidNumber( $uidNumber) 
    {
        $sr = ldap_search($this->_ress, $this->config['base_dn'], '(uidNumber=' . $uidNumber . ')', array('mail'));
        if (!$sr)
            return false;

        $return = ldap_get_entries($this->_ress, $sr);
        return isset($return[0]['mail'][0]) ? $return[0]['mail'][0] : array();
    }

    /**
     * Retorna em um array os endereços de e-mails alternativos da conta pelo uidNumber 
     * 
     * @license    http://www.gnu.org/copyleft/gpl.html GPL 
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br) 
     * @sponsor    Caixa Econômica Federal 
     * @author     Cristiano Corrêa Schmidt 
     * @param      int $uidNumber uidNumber da conta 
     * @return     Array 
     * @access     public 
     */
    public function getMailAlternateByUidNumber( $uidNumber) 
    {
        $sr = ldap_search($this->_ress, $this->config['base_dn'], '(uidNumber=' . $uidNumber . ')', array('mailAlternateAddress'));
        if (!$sr)
            return false;

        $return = $this->formatEntries(ldap_get_entries($this->_ress, $sr));
        return isset($return[0]['mailalternateaddress']) ? $return[0]['mailalternateaddress'] : array();
    }

    /**
     * Escape string for use in LDAP search filter.
     *
     * @link http://www.php.net/manual/de/function.ldap-search.php#90158
     * See RFC2254 for more information.
     * @link http://msdn.microsoft.com/en-us/library/ms675768(VS.85).aspx
     * @link http://www-03.ibm.com/systems/i/software/ldap/underdn.html
     */
    private function escape($str)
    {
        $metaChars = array('*', '(', ')', '\\', chr(0));
        $quotedMetaChars = array();
        foreach ($metaChars as $key => $value) 
            $quotedMetaChars[$key] = '\\'.str_pad(dechex(ord($value)), 2, '0');
        
        $str = str_replace($metaChars, $quotedMetaChars, $str);
        return $str;
    }

    /**
     * Formata o retorno do ldap para algo mais legivel
     * 
     * @license    http://www.gnu.org/copyleft/gpl.html GPL 
     * @author     Consórcio Expresso Livre - 4Linux (www.4linux.com.br) e Prognus Software Livre (www.prognus.com.br) 
     * @sponsor    Caixa Econômica Federal 
     * @author     Cristiano Corrêa Schmidt 
     * @param      array entradas a serem formatadas
     * @return     array 
     * @access     private 
     */
    private function formatEntries(array $entries)
    {
        $return = array();
        for ( $i=0; $i < $entries['count']; $i++ ) 
        {
            if(is_array($entries[$i]))
                $return[] =  $this->formatEntries($entries[$i]);
            else if(isset($entries[$entries[$i]]))
                $return[$entries[$i]] = $this->formatEntries($entries[$entries[$i]]);
            else if($entries['count'] === 1)
                return $entries[$i];  
            else  
                $return[] = $entries[$i];

            if(isset($entries['dn']))
                $return['dn'] = $entries['dn'];
        }
    
        return $return;
    }

    public function creatRule( $cn , $members , $gidNumber , $desc )
    {
        $role = array();
        $role['objectClass'][] = 'posixGroup';
        $role['objectClass'][] = 'top';
        $role['objectClass'][] = 'phpgwAccount';
        $role['phpgwAccountType']  = 'r';
        $role['memberUid'] = $members;
        $role['gidNumber'] = $gidNumber;
        $role['cn'] = $cn;
        $role['description'] = $desc;

        $this->bindAdmin();

        $dn = 'cn='.$role['cn'].','.$this->config['roleContext'];
        if (ldap_add($this->_ress, $dn , $role))
        {
            $this->close();
            return true;
        }
        else
            throw new \Exception(ldap_error($this->_ress));

        return false;
    }

    public function add($data , $dn )
    {
        $this->bindAdmin();

        if (ldap_add($this->_ress, $dn , $data))
        {
            $this->close();
            return true;
        }
        else
            throw new \Exception(ldap_error($this->_ress));

        return false;
    }

    function close()
    {
        return ldap_close($this->_ress);
    }

    function stemFilter($search, $params , $conditional) {
        $search = str_replace(' ', '*', $search);

        if (!is_array($params))
            $params = array($params);

        foreach ($params as $i => $param)
            $params[$i] = "($param=*$search*)";

        return '(' . $conditional . implode('', $params) . ')';
    }

    public function getConfig( $config )
    {
        return isset($this->config[$config]) ? $this->config[$config] : false;
    }

    public function getFilter( $filter )
    {
        return isset($this->filters[$filter]) ? $this->filters[$filter] : false;
    }

    public function getRulesByUid( $uid , array $atributes = array())
    {
        $sr = ldap_search($this->_ress, $this->config['roleContext'], '(&'.$this->filters['role'].'(memberUid=' . $uid . '))', $atributes );
        if (!$sr)
            return false;

        return $this->formatEntries(ldap_get_entries($this->_ress, $sr));
    }
}
