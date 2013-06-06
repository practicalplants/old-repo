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
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, false);
		curl_setopt($ch, CURLOPT_COOKIE, implode("; ",$cookies) ); 
		$res = curl_exec($ch);
		//echo $res; exit;
		curl_close($ch);
	}
	
	public function deleteCookies(){
		//set the cookie on the current domain (this seems to be the ONLY way to get setcookie to not add a . to the start of the domain!)
		setcookie('Wiki', false, -1, '/'); 
		setcookie('Wiki', false, -1, '/',$this->_cookieDomain);
		if(strpos($this->_cookieDomain, '.')===0){
			$dotless = substr($this->_cookieDomain, 1);
			setcookie('Wiki', false, -1, '/', $dotless);
		}
	}
}