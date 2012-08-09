<?php

class Application_Model_Integration_Vanilla {
	
	public function __construct($domain){
		 $this->_cookieDomain = $domain;
	}
	
	public function onAuthenticate(){
		$this->deleteCookies();
	}
	
	public function onShareSession(){
	
	}
	
	public function onDestroySession(){
		$this->deleteCookies();
	}
	
	public function deleteCookies(){
		setcookie('Vanilla', false, 315554400, '/',$this->_cookieDomain);
		setcookie('Vanilla-Volatile', false, 315554400, '/',$this->_cookieDomain);
		setcookie('VanillaProxy', false, 315554400, '/',$this->_cookieDomain);
		setcookie('Vanilla-Vv', false, 315554400, '/',$this->_cookieDomain);
	}	
}