<?php

class Application_Model_Integration_MediaWiki {
	
	public function __construct(){
		 $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		 $options = $bootstrap->getOptions();
		 $this->_cookieDomain = $options['app']['cookiedomain'];
	}
	
	public function onShareSession(){
	
	}
	
	public function onAuthenticate(){
		$this->deleteCookies();
	}
	
	public function onDestroySession(){
		$this->deleteCookies();
	}
	
	public function deleteCookies(){
		setcookie('Practical-Plants-Wiki', false, 315554400, '/',$this->_cookieDomain);
		//setcookie('plantswikiUserName', false, 315554400, '/');
		//setcookie('plantswikiLoggedOut', false, 315554400, '/');
	}
}