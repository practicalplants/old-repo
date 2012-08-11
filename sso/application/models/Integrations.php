<?php
include_once(dirname(__FILE__).'/integration/MediaWiki.php');
include_once(dirname(__FILE__).'/integration/Vanilla.php');

class Application_Model_Integrations {
	
	public function __construct(){
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$options = $bootstrap->getOptions();
		$this->_cookieDomain = $options['app']['cookiedomain'];
		$this->integrations = array(
			new Application_Model_Integration_Mediawiki($this->_cookieDomain),
			new Application_Model_Integration_Vanilla($this->_cookieDomain)
		);
	}
	
	public function onAuthenticate(){
		//set an auth cookie for masthead to use to choose whether to display 'login' or 'account' on wiki/community
		//setcookie('SSO-Authed', true, time()+2512345, '/', $this->_cookieDomain);
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
		//setcookie('SSO-Authed', false, 315554400, '/', $this->_cookieDomain);
	}
}