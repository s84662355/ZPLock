<?php
namespace ZPLock;
use  Zookeeper;

class LockControl{


	private $zk ;

	private $acl = [];

    private $lock_array = [];

	public function __construct($config)
	{
		$this->zk = new Zookeeper($config['host']);

		if(!empty($config['auth'])){
             $this->zk -> addAuth($config['auth']['scheme'],"{$config['auth']['user']}:{$config['auth']['password']}");
		}  
		$this->acl =  $config['acl'];
	}


	public function lock($name,$type,$wait_time = 10  )
	{
		if(empty( $this->lock_array[$name] )){
			$lock = new  ZP_LOCK($this->zk,$this->acl,$name,$type);
			if($lock -> lock( $wait_time  )){
				 $this->lock_array[$name] = $lock;
				 return true;
		    }
		}else{
			if( $this->lock_array[$name]-> getType() == $type){
				return true;
			}
		}

		return false;
	}

	public function unlock($name)
	{
        if($this->lock_array[$name] ->unlock()) {
        	unset($this->lock_array[$name]);
        	return true;
        }
        return false;
         
	}

	public function getUpdateLock($name,$wait_time = 0)
	{
		 return $this->lock($name,ZP_LOCK::UPDATE,$wait_time);
	}

	public function getShareLock($name,$wait_time = 0)
	{
		return  $this->lock($name,ZP_LOCK::SHARE,$wait_time);
	}


    public function __destruct() 
    {
        $this->zk->close();
    }

}