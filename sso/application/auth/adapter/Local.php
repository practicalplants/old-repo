<?php 
/**
 * Webjawns_Auth_Adapter
 *
 * @package Webjawns
 * @subpackage Auth
 */
class My_Auth_Adapter_Local implements Zend_Auth_Adapter_Interface {
    /**
     * The username
     *
     * @var string
     */
    protected $_identity = null;
 
    /**
     * The password
     *
     * @var string
     */
    protected $_credential = null;
 
    /**
     * Users database object
     *
     * @var Model_Db_Table_Users
     */
    protected $_usersModel = null;
 
    public function __construct($identity, $password, $usersModel) {
        /*if (!$usersModel instanceof Webjawns_Db_Table_Abstract) {
            throw new Zend_Auth_Exception('No adapter found for $usersModel');
        }*/
 
        $this->_identity = $identity;
        $this->_credential = $password;
        $this->_usersModel = $usersModel;
    }
 
    public function authenticate() {
        // Fetch user information according to username
        if (!$user = $this->_usersModel->getUserBy('email', $this->_identity) ) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $this->_identity,
                array('Invalid email address')
            );
        }

        // Pass given credential through PHPass and check whether or not the hash matches
        if (!$this->_usersModel->checkPassword($this->_credential, $user->password, $user->user_id)) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $this->_identity,
                array('Incorrect password')
            );
        }
        
        // Check user is confirmed
        if (!$user->email_confirmed) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $this->_identity,
                array('Your account is awaiting email confirmation.')
            );
        }else{
        
	        // If user is confirmed, check user is active & enabled
	        if (!$user->active || !$user->enabled) {
	            return new Zend_Auth_Result(
	                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
	                $this->_identity,
	                array('Your account is disabled. Please contact us for clarification if you believe this to be in error.')
	            );
	        }
        
        }
        
        $p = array(
        	'id'=>$user->user_id,
        	'email'=>$user->email,
        	'username'=>$user->username,
        	'name'=>$user->display_name
        );
 		
        // Success!
        return new Zend_Auth_Result(
            Zend_Auth_Result::SUCCESS,
            $user->email,
            array('user'=>$p)
        );
    }
 
}