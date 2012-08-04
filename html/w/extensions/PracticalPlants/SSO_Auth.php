<?php
/**
 * Authentication plugin interface
 *
 * Copyright © 2004 Brion Vibber <brion@pobox.com>
 * http://www.mediawiki.org/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

/**
 * Authentication plugin interface. Instantiate a subclass of AuthPlugin
 * and set $wgAuth to it to authenticate against some external tool.
 *
 * The default behavior is not to do anything, and use the local user
 * database for all authentication. A subclass can require that all
 * accounts authenticate externally, or use it only as a fallback; also
 * you can transparently create internal wiki accounts the first time
 * someone logs in who can be authenticated externally.
 */
 


class PracticalPlants_SSO_Auth extends AuthPlugin {
	
	
	public function __construct(){
		global $wgHooks;
		
		
		$wgHooks['UserLoadFromSession'][] = array($this,'userLoadFromSession');
		$wgHooks['UserLogout'][] = array($this,'userLogout');
		
		
		$this->sso_url = $ssoUrl;
		
	}
	
	public static function startSession(){
		global $wgSessionName;
		/*Forcing session start for SSOAuth to work properly */
		if(!isset($_SESSION)){
			//session_name('Practical-Plants-Wiki');
			session_name( $wgSessionName );
			session_start();
		}
	}
	
	/* Make a curl request to the SSO application - this will send all current 
	cookies including the domain-wide SSO cookie, and so can allow us to piggyback on the SSO session */
	public function userLoadFromSession( $user, &$result ) {
				
		//echo '<pre>'; print_r($user); exit;
		if(!isset($_SESSION)){
			self::startSession();
		}
		if(isset($_SESSION['sso_user'])){
			$sso_user = $_SESSION['sso_user'];
		}else{
			$cookies = array();
			foreach($_COOKIE as $k=>$v){
				$cookies[] = $k.'='.$v;
			}
			$ch = curl_init($this->sso_url.'integration/mediawiki/share-session');
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIESESSION, false);
			curl_setopt($ch, CURLOPT_COOKIE, implode("; ",$cookies) ); 
			$res = curl_exec($ch);
			// echo $res; exit;
			curl_close($ch);
			
			if($res){
				$sso_user = json_decode($res);
				$_SESSION['sso_user'] = $sso_user;
			}
		}
		
		
		if(isset($sso_user) && isset($sso_user->id)){
			$username = $this->getCanonicalName($sso_user->username);
			//$user->loadDefaults($sso_user->username);				
			$user->mName = $username;
			$userId = $user->idForName();
			
	        if ( 0 == $userId ) {
	        	$this->createUserFromSSO( $user, $sso_user ); # see below
	        } else {
	        	$user->setId( $userId );
	        }
	 
	        # Finally, automagically login based on the corporate credentials
	        $user->loadfromDatabase();
	        $user->saveToCache();           # this also loads the user's group membership
	        $wgUser = $user;
			
			$result = true;
			//echo '<pre>';
			//print_r($sso_user);
			//print_r($user);
			//exit;
		}
		return true;
	}
	
	public 	function userLogout( &$user ) {
		/*$cookies = array();
		foreach($_COOKIE as $k=>$v){
			$cookies[] = $k.'='.$v;
		}
		$ch = curl_init($this->sso_url.'integration/mediawiki/destroy-session');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIESESSION, false);
		curl_setopt($ch, CURLOPT_COOKIE, implode("; ",$cookies) ); 
		$res = curl_exec($ch);
		 echo $res; exit;
		curl_close($ch);
		*/
		unset($_SESSION['sso_user']);
		
		header('Location: '.$this->sso_url.'logout');
		
		return true;
	}
	
	
	
	public function createUserFromSSO($user, $sso_user){
		$user->loadDefaults( $user->mName );
        //$user->mRealName                = $realName;
        $user->mEmail                   = $sso_user->email;
        $user->mEmailAuthenticated      = wfTimestampNow();
        $user->setOption( 'rememberpassword', 1 );                      # implicitly loads other default options
        $user->addToDatabase();                                         # sets mId for us
        
        # Update user count
        $ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
        $ssUpdate->doUpdate();
	}

	
	/**
	 * Check whether there exists a user account with the given name.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @return bool
	 */
	public function userExists( $username ) {
		$ch = curl_init($this->sso_url.'integration/mediawiki/username-exists/'.$username);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = (bool) curl_exec($ch);
		curl_close($ch);
		if($res===false){
			return false;
		}
		
		return true;
	}

	/**
	 * Check if a username+password pair is a valid login.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @param $password String: user password.
	 * @return bool
	 */
	public function authenticate( $username, $password ) {
		
		/*$ch = curl_init($this->sso_url.'integration/mediawiki/authenticate/');
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('username'=>$username,'password'=>$password));
		
		$res = curl_exec($ch);
		
		//echo $res; exit;
		curl_close($ch);
		if($res === 'true'){
			return true;
		}*/
		
		return false;
	}

	/**
	 * Modify options in the login template.
	 *
	 * @param $template UserLoginTemplate object.
	 * @param $type String 'signup' or 'login'.
	 */
	public function modifyUITemplate( &$template, &$type ) {
		# Override this!
		//print_r($template); exit;
		/*$template->set( 'usedomain', false );
		
		//disable the mail new password box
	    $template->set("useemail", false);
	 
	    //disable 'remember me' box
	    $template->set("remember", false);
	 
	    $template->set("create", false);
	 
	    $template->set("domain", true);*/
	}

	/**
	 * Set the domain this plugin is supposed to use when authenticating.
	 *
	 * @param $domain String: authentication domain.
	 */
	public function setDomain( $domain ) {
		$this->domain = $domain;
	}

	/**
	 * Check to see if the specific domain is a valid domain.
	 *
	 * @param $domain String: authentication domain.
	 * @return bool
	 */
	public function validDomain( $domain ) {
		# Override this!
		return true;
	}

	/**
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object
	 */
	public function updateUser( &$user ) {
		# Override this and do something
		return true;
	}

	/**
	 * Return true if the wiki should create a new local account automatically
	 * when asked to login a user who doesn't exist locally but does in the
	 * external auth database.
	 *
	 * If you don't automatically create accounts, you must still create
	 * accounts in some way. It's not possible to authenticate without
	 * a local account.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return Boolean
	 */
	public function autoCreate() {
		return false;
	}

	/**
	 * Allow a property change? Properties are the same as preferences
	 * and use the same keys. 'Realname' 'Emailaddress' and 'Nickname'
	 * all reference this.
	 *
	 * @param $prop string
	 *
	 * @return Boolean
	 */
	public function allowPropChange( $prop = '' ) {
		if ( $prop == 'realname' && is_callable( array( $this, 'allowRealNameChange' ) ) ) {
			return false;//$this->allowRealNameChange();
		} elseif ( $prop == 'emailaddress' && is_callable( array( $this, 'allowEmailChange' ) ) ) {
			return false;//$this->allowEmailChange();
		} elseif ( $prop == 'nickname' && is_callable( array( $this, 'allowNickChange' ) ) ) {
			return false;//$this->allowNickChange();
		} else {
			return true;
		}
	}

	/**
	 * Can users change their passwords?
	 *
	 * @return bool
	 */
	public function allowPasswordChange() {
		return false;
	}

	/**
	 * Set the given password in the authentication database.
	 * As a special case, the password may be set to null to request
	 * locking the password to an unusable value, with the expectation
	 * that it will be set later through a mail reset or other method.
	 *
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @param $password String: password.
	 * @return bool
	 */
	public function setPassword( $user, $password ) {
		return true;
	}

	/**
	 * Update user information in the external authentication database.
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @return Boolean
	 */
	public function updateExternalDB( $user ) {
		return true;
	}

	/**
	 * Check to see if external accounts can be created.
	 * Return true if external accounts can be created.
	 * @return Boolean
	 */
	public function canCreateAccounts() {
		return false;
	}

	/**
	 * Add a user to the external authentication database.
	 * Return true if successful.
	 *
	 * @param $user User: only the name should be assumed valid at this point
	 * @param $password String
	 * @param $email String
	 * @param $realname String
	 * @return Boolean
	 */
	public function addUser( $user, $password, $email = '', $realname = '' ) {
		return true;
	}

	/**
	 * Return true to prevent logins that don't authenticate here from being
	 * checked against the local database's password fields.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return Boolean
	 */
	public function strict() {
		return true;
	}

	/**
	 * Check if a user should authenticate locally if the global authentication fails.
	 * If either this or strict() returns true, local authentication is not used.
	 *
	 * @param $username String: username.
	 * @return Boolean
	 */
	public function strictUserAuth( $username ) {
		return false;
	}

	/**
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object.
	 * @param $autocreate Boolean: True if user is being autocreated on login
	 */
	public function initUser( &$user, $autocreate ) {
		# Override this to do something.
		/*if($autocreate){
			if(isset($this->sso_user['email']))
				$user->setEmail($this->sso_user['email']);
			
			//$user->setRealName("John Smith");
		 
		    //if using MediaWiki 1.5, we can set some e-mail options
		   
		    $user->mEmailAuthenticated = time();
		   
		    //turn on e-mail notifications by default
		    $user->setOption('enotifwatchlistpages', 1);
		    $user->setOption('enotifusertalkpages', 1);
		    $user->setOption('enotifminoredits', 1);
		    $user->setOption('enotifrevealaddr', 1);
		    
		    $autocreate = true;
	    }*/
	}

	/**
	 * If you want to munge the case of an account name before the final
	 * check, now is your chance.
	 */
	public function getCanonicalName( $username ) {
		return ucfirst( strtolower($username) );
	}

	/**
	 * Get an instance of a User object
	 *
	 * @param $user User
	 *
	 * @return AuthPluginUser
	 */
	public function getUserInstance( User &$user ) {
		return new AuthPluginUser( $user );
	}

	/**
	 * Get a list of domains (in HTMLForm options format) used.
	 *
	 * @return array
	 */
	public function domainList() {
		return array();
	}
}

/*class PracticalPlants_AuthUser extends AuthPluginUser {
	function __construct( $user ) {
		# Override this!
	}

	public function getId() {
		# Override this!
		return -1;
	}

	public function isLocked() {
		# Override this!
		return false;
	}

	public function isHidden() {
		# Override this!
		return false;
	}

	public function resetAuthToken() {
		# Override this!
		return true;
	}
}*/
