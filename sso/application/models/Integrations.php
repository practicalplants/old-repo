<?php
include_once(dirname(__FILE__).'integration/MediaWiki.php');
include_once(dirname(__FILE__).'integration/Vanilla.php');

class Application_Model_Integrations {
	
	public function __construct(){
		$this->integrations = array(
			new Application_Model_Integration_Mediawiki(),
			new Application_Model_Integration_Vanilla()
		);
	}
	
	public function onAuthenticate(){
		//set an auth cookie for masthead to use to choose whether to display 'login' or 'account' on wiki/community
		setcookie('SSO-Authed', true, time()+2512345, '/');
		foreach($this->integrations as $i){
			$i->onAuthenticate();
		}
	}
	
	public function onDestroySession(){
		foreach($this->integrations as $i){
			$i->onDestroySession();
		}
		$this->deleteCookies();
	}
	
	public function deleteCookies(){
		setcookie('SSO-Authed', false, 315554400, '/');
	}
}