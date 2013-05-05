<?php


/* NOT IN USE */
class Application_Model_User {
 
    public $user_id, $username, $email, $display_name;
 
    public function __construct($data){
    	foreach($data as $k => $v){
    		if( !in_array($k,array('user_id','username','email','display_name')) ){
    			throw new Exception('Insufficient data supplied to User constructor. '.$k.' is a required value.');
    		}else{
    			$this->{$k} = $v;
    		}
    	}
    	$this->construct_data = $data;
    	
    }

    
    public function authenticate(){

    }
    
    public function associateProvider(){
        
    }
    
    //force authentication
    public function setAuthenticated(){
    	
    }
 	
 	public function authenticateLocal($identity, $password){
 		
 	}
 	
 	public function authenticateExternal($provider){
 		
 	}
 	
 	public function createLocalUser(){
 		
 	}
 	
}