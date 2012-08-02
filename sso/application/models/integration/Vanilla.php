<?php

class Application_Model_Integration_Vanilla {
	
	public function onAuthenticate(){
		$this->deleteCookies();
	}
	
	public function onShareSession(){
	
	}
	
	public function onDestroySession(){
		$this->deleteCookies();
	}
	
	public function deleteCookies(){
		setcookie('Vanilla', false, 315554400, '/');
		setcookie('Vanilla-Volatile', false, 315554400, '/');
	}	
}