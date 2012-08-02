<?php

class Application_Model_Integration_MediaWiki {
	
	
	public function onShareSession(){
	
	}
	
	public function onAuthenticate(){
		$this->deleteCookies();
	}
	
	public function onDestroySession(){
		$this->deleteCookies();
	}
	
	public function deleteCookies(){
		setcookie('Practical-Plants-Wiki', false, 315554400, '/');
		//setcookie('plantswikiUserName', false, 315554400, '/');
		//setcookie('plantswikiLoggedOut', false, 315554400, '/');
	}
}