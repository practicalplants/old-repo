<?php
include_once(dirname(__FILE__).'/integration/MediaWiki.php');
include_once(dirname(__FILE__).'/integration/Vanilla.php');

class Application_Model_Integrations {
	
	public function __construct($opts=array()){
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$settings = $bootstrap->getOptions();
		$this->_cookieDomain = $settings['app']['cookiedomain'];
		$this->persist = (isset($opts['persist']) && $opts['persist']===true) ? true : false;
		$this->integrations = array(
			new Application_Model_Integration_MediaWiki($this->_cookieDomain),
			new Application_Model_Integration_Vanilla($this->_cookieDomain)
		);
		
	}
	
	public function onAuthenticate(){
		if($this->persist){
			setcookie('SSO-Persist', true, time()+31536000, '/', $this->_cookieDomain);
		}
		setcookie('SSO-Authed', true, time()+31536000, '/', $this->_cookieDomain);
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
		/*if($this->persist){
			setcookie('SSO-Persist', false, time()-60, '/', $this->_cookieDomain);
		}*/
		setcookie('SSO-Persist', false, time()-60, '/', $this->_cookieDomain);
		setcookie('SSO-Authed', false, time()-60, '/', $this->_cookieDomain);
	}
}