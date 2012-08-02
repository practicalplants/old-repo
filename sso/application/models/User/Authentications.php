<?php

class Application_Model_User_Authentications extends Zend_Db_Table_Abstract {
 
    protected $_name = 'user_authentication';
 
    /**
     * Get user by a particular field.
     *
     * @param string $field
     * @param string $value
     * @return object User row object
     */
    public function getAuthentication($provider, $identity) {
 		$authentication = $this->fetchRow($this->select()->where('provider = ?', $provider)->where('identity = ?', $identity));
        if (! $authentication)
            return false;
 		
        return $authentication;
    }
    
    public function getAuthenticationByUserAndProvider($user, $provider){
    	$authentication = $this->fetchRow($this->select()->where('provider = ?', $provider));
    	return $authentication;
    }
    
    public function addAuthentication(Zend_Db_Table_Row $user,$provider,$identity){
    	$row = array('user_id'=>$user->user_id, 'provider'=>$provider, 'identity'=>$identity);
    	$id = $this->insert($row);
    	if($id){
    		return $id;
    	}
    	return false;
    }
 	
  
}