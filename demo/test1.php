<?php

require_once  dirname(__DIR__ ). '/vendor/autoload.php';

 
use ZPLock\LockControl;

$config = [
      'host' => '127.0.0.1:2181',  
      'acl'  =>            [
                'perms'  => Zookeeper::PERM_ALL,
                'scheme' => 'world',
                'id'     => 'anyone',
            ]
      /*
      'auth' => [
            'scheme' => 'world',   
            'user'   => 'user0',   
            'password' => 'password0',   
      ],
      */

];
$cro = new LockControl($config);

var_dump( $cro->getShareLock("aaa111aa", 15) ) ;


sleep(5);