# ZPLock
这是一个用  Zookeeper 实现分布式锁的项目
但是有美中不足的地方就是 zookeeper的扩展可能存在bug没有办法监听连接断开的事件

composer require chenjiahao/zp-lock


在配置文件app.php加入


    'providers' => [
        
         ZPLock\ZP_LOCKServiceProvider::class,
         .
         .
         ..
         .
    ],



php artisan vendor:publish 
选择
ZPLock\ZP_LOCKServiceProvider::class


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


共享锁
$name,$wait_time = 0
 app('ZPLOCK') ->getShareLock("aaa111aa", 15) 

独占锁
$name,$wait_time = 0
  app('ZPLOCK') ->getUpdateLock("aaa111aa", 15) 

  app('ZPLOCK') ->unlock("aaa111aa");