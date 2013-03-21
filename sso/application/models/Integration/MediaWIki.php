<?php

class Application_Model_Integration_MediaWiki {
	
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


		//fake a request to UserLogout action on the wiki
		$cookies = array();
		foreach($_COOKIE as $k=>$v){
			$cookies[] = $k.'='.$v;
		}
		$ch = curl_init($_SERVER['HTTP_HOST'].'/wiki/Special:UserLogout');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, false);
		curl_setopt($ch, CURLOPT_COOKIE, implode("; ",$cookies) ); 
		$res = curl_exec($ch);
		// echo $res; exit;
		curl_close($ch);
	}
	
	public function deleteCookies(){
		setcookie('Wiki', false, time()-9999999, '/',$this->_cookieDomain);
		if(strpos($this->_cookieDomain, '.')===0){
			$dotless = substr($this->_cookieDomain, 1);
			setcookie('Wiki', false, time()-9999999, '/',$dotless);
		}
	}
}