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
		setcookie('Vanilla', false, time()-60, '/',$this->_cookieDomain);
		setcookie('Vanilla-Volatile', false, time()-60, '/',$this->_cookieDomain);
		setcookie('Vanilla-Vv', false, time()-60, '/',$this->_cookieDomain);
	}	
}