<?php

namespace Expresso\ExpressoBundle\Service;

class Find extends \Memcache
{
    var $expiration;

    public function __construct( $expiration , $servers )
    {
        $this->expiration = $expiration;

        foreach ($servers as $server)
        {
            $this->addserver( $server['host'] , $server['port']);
        }
    }

    public function create( $url , $data )
    {
        $token = $url .'/'.  $this->generateToken(10);

        return parent::set($token , $data, ( is_bool( $data ) || is_int( $data ) || is_float( $data ) ) ? false : MEMCACHE_COMPRESSED, $this->expiration ) ? $token : false;
    }

    function generateToken($length)
    {
        $random= "";
        srand((double)microtime()*1000000);
        $char_list = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $char_list .= "abcdefghijklmnopqrstuvwxyz";
        $char_list .= "1234567890";

        for($i = 0; $i < $length; $i++)
        {
            $random .= substr($char_list,(rand()%(strlen($char_list))), 1);
        }

        return $random;
    }

}
