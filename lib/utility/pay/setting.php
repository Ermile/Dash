<?php
namespace dash\utility\pay;


class setting
{

    public static function set()
    {
        // set default timeout for socket
        ini_set("default_socket_timeout", 10);
        ini_set('soap.wsdl_cache_enabled',0);
        ini_set('soap.wsdl_cache_ttl',0);
    }
}
?>