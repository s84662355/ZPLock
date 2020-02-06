<?php
namespace ZPLock;

use Zookeeper;
use Throwable;


class ZP_LOCK {

	protected  $zk;
	protected  $aclArray; 

    public const SHARE = 0;
    public const UPDATE = 1;

    private $is_lock = 0;

    private $node = null;

    private $name;

    private $father_node_name;

    private $children = [];

    private $type ;


	public function __construct(Zookeeper $zk,$aclArray,$name,$type)
	{
        $this->zk =  $zk;
        $aclArray['perms'] =  Zookeeper::PERM_ALL;
	    $this->aclArray[] = $aclArray ;
	    $this->type = $type;
        $this->name = $name;
	}

    public function lock( $wait_time = 10)
    {
    	$this->father_node_name = '/'.__CLASS__.$this->name;
    	

    	try{
            $this->zk->create(
            	$this->father_node_name, 
            	null, 
            	$this->aclArray, 
            	0
            	);
    	}catch(Throwable $err){
          ///  var_dump($err->getMessage ());
    	}

    	return $this->getLock(  $wait_time);
    }

    public function unlock()
    {
    	return $this->zk->delete( $this->node );
    }


    private function getLock(  $wait_time)
    {
    	$lock_data = [
              'type' => $this->type,
              'is_get' => 0,
    	];

    	$node_name  = $this->father_node_name .'/1';
        
        $this->node = 
                      $this->zk->create(
                                        	$node_name, 
                                        	json_encode($lock_data), 
                                        	$this->aclArray,
                                        	Zookeeper::EPHEMERAL | Zookeeper::SEQUENCE
        	                           );
    
 


                    ///  var_dump(   $this->node);

        $this->getLockOperation();
  
 
        $wait_time = $wait_time * 1000;


        while(   $wait_time >= 0    ){
            
            if($this->is_lock == 1){
                      
            	return true;
            }

            usleep(100 * 1000);

           

            $wait_time =  $wait_time - 100;
        }

        return false;

  
    }

 


    private function getLockOperation()
    {

  

        $this->children = $this->zk->getchildren($this->father_node_name);
        rsort($this->children ,1);
        $node_location = 0;
        foreach ($this->children as $key => $value) {

         //   var_dump($value);
            if($this->node   == $this->father_node_name.'/'.$value){
                    $node_location = $key;
            }	 
        }

    	$location = $node_location + 1;
    	if(empty($this->children[$location])){

            
                $this->setLock();
    		    return true;
    	}


        switch ($this->type) {
        	case static::SHARE:
        	    $this->getReadLock($location );
        		break;
            case static::UPDATE:
        		$this->getWriteLock($location );
        		break;  	
     
        }

    }


    private function getReadLock($location )
    {
        $data = $this->setWatch($location);
        if(!empty($data)){
              $data = json_decode($data,true);
              if($data['type'] == static::SHARE && $data['is_get'] == 1){
                $this->setLock();
              	return true;

              }
        }else{
        	    $this->setLock();
    		    return true;
        }
    }

    public function watchLock($type, $state, $key)
    {
       
         if ($this->is_lock == 0) {
                     $this->getLockOperation();
         }
     
    }

    private function getWriteLock($location )
    {
        $data = $this->setWatch($location);
        if(empty($data)){
                $this->setLock();
    		    return true;   
        }
    }


    private function setLock()
    {
			$lock_data = [
	              'type' => $this->type,
	              'is_get' => 1,
	    	];
		    $this->zk->set($this->node, json_encode($lock_data));
		    $this->is_lock = 1;
    }

    private function setWatch($location)
    {
    	$data = null;
    	for (;!empty($this->children[$location]);$location++) { 
    	     
    	        try{
                    $data = $this->zk->get( $this->father_node_name.'/'.$this->children[$location] , array($this, 'watchLock'));
                    break;
		    	}catch(Throwable $err){
		        
		    	}
    	}

        
    	return $data;
    }

    public function getType()
    {
        return $this->type;
    }
}