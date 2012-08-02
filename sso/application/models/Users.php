<?php

class Application_Model_Users extends Zend_Db_Table_Abstract {
 
    protected $_name = 'user';
    
    protected $_user_definable_fields = array('username','email','display_name','password');
    protected $_required_fields = array('username','email','password');
 
    /**
     * Get user by a particular field.
     *
     * @param string $field
     * @param string $value
     * @return object User row object
     */
    public function getUserBy($field, $value) {
        switch ($field) {
        	case 'id':
        	case 'user_id':
        		$field = 'user_id';
        		break;
            case 'username':
                $field = 'username';
                break;
            case 'email':
            	$field = 'email';
            	break;
            default:
                return false;
        }
 		
        if (!$user = $this->fetchRow($this->select()->where($field.' = ?', $value)) )
            return false;
 		
        return $user;
    }
    
    public function getUserByAuthIdentity($provider,$identity){
    	$auths = new Application_Model_User_Authentications();
    	if($auth = $auths->getAuthentication($provider,$identity)){
    		if($user = $this->getUserBy('user_id',$auth->user_id)){
    			return $user;
    		}
    	}
    	return false;
    }
    
    public function createUser($props){
    	$user = array('enabled'=>1, 'active'=>0);
    	foreach($this->_required_fields as $f){
    		if(!array_key_exists($f,$props)){
    			throw new Exception("Required fields were not filled in");
    		}
    	}
    	if( $this->getUserBy('email', $props['email']) )
    		throw new Exception('Account already exists with this email address');
    	if( $this->getUserBy('username',$props['username']) )
    		throw new Exception('This username is already in use');

    	foreach($props as $n=>$p){
    		if(in_array($n,$this->_user_definable_fields)){
    			$user[$n] = $p;
    		}
    	}
    	
    	$user['password'] = $this->hashPassword($user['password']);
    	//$user->register_time = 'NOW()';
    	//$user->register_ip = $_SERVER['REMOTE_ADDR'];
    	//print_r($user);exit;
    	$id = $this->insert($user);
    	if($id){
    		return $this->getUserBy('user_id',$id);
    	}
    	return false;
    }
    
    public function activateUser($id,$code){
    	if(!$user = $this->getUserBy('id',$id)){
    		return false;
    	}
    	$match_code = substr(md5($user->register_time.$user->register_ip),10,5);
    	if($code !== $match_code){
    		return false;
    	}
    	if($user->email_confirmed==0){
    		$this->setUserActive($id);
    	}
    	
    	return true;
    }
    
 	public function setUserActive($id){
 		$this->update(
 			array('email_confirmed'=>1,'active'=>1,'enabled'=>1), 
 			array('user_id'=>$id)
 		);
 	}
    /**
     * Create a haash (encrypt) of a plain text password.
     *
     * @param string $password Plain text user password to hash
     * @return string The hash string of the password
     */
    public function hashPassword($password) {
        return $this->hasher()->HashPassword($password);
    }
    
 
    /**
     * Compare the plain text password with the $hashed password.
     *
     * @param string $password
     * @param string $hash The hashed password
     * @param int $user_id The user row ID
     * @return bool True if match, false if no match.
     */
    public function checkPassword($password, $hash, $user_id = '') {
        // Check if we are still using regular MD5 (32 chars)
        if (strlen($hash) <= 32) {
            $check = ($hash == md5($password));
            if ($check && $user_id) {
                // Rehash using new PHPass-generated hash
                $this->setPassword($password, $user_id);
                $hash = $this->hashPassword($password);
            }
        }
 
        $check = $this->hasher()->CheckPassword($password, $hash);
 
        return $check;
    }
 
    /**
     * Set password for specified user ID.
     *
     * @param string $password The user's password (plain text).
     * @param int $user_id The user row ID.
     * @return void
     */
    public function setPassword($password, $user_id) {
        $hash = $this->hashPassword($password);
 
        $this->update(
            array('password' => $hash),
            array('user_id' => $user_id)
        );
    }
 
    /**
     * Checks for Webjawns_PasswordHash in registry. If not present, creates PHPass object.
     *
     * @uses Zend_Registry
     * @uses Webjawns_PasswordHash
     *
     * @return Webjawns_PasswordHash PHPass
     */
    public function hasher() {
        if (!Zend_Registry::isRegistered('hasher')) {
            Zend_Registry::set('hasher', new PasswordHash_PasswordHash(8, true));
        }
        return Zend_Registry::get('hasher');
    }
   
 
}