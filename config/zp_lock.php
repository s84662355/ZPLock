<?php

return [
      'host' => env('ZP_LOCK_HOST','locahost:2181'),  
      'acl'  => [   
            'scheme' =>  env('ZP_LOCK_ACL_SCHEME', 'world'),   
            'id'     =>  env('ZP_LOCK_ACL_ID', 'id'), 
      ],
      'auth' => [
            'scheme' => env('ZP_LOCK_AUTH_SCHEME', 'world'),   
            'user' =>  env('ZP_LOCK_AUTH_USER', 'user0'),   
            'password' => env('ZP_LOCK_AUTH_PASSWORD', 'password0'),   
      ],

];
