<?php

class UserController extends Zend_Controller_Action {

    /**
     * Application keys from appkeys.ini
     * 
     * @var Zend_Config 
     */
    protected $_keys;
    
	public function preDispatch() {
	
		if (Zend_Auth::getInstance()->hasIdentity()) {
			switch($this->getRequest()->getActionName()){
				case 'logout':
				case 'create-from-external':
				case 'associate-provider':
					break;
				default:
					if(isset($_GET['redirect'])){
						header('Location: '.$_GET['redirect']);
					}else{
						$this->_redirect()->gotoRoute(array(),'home');
					}
			}
		}else{
			
		}
		if(Zend_Registry::isRegistered('logger')){
		  $this->logger = Zend_Registry::get('logger');
		}
	}
	
	protected function log($msg){
        if($this->logger && $this->logger instanceof Zend_Log){
          $this->logger->info($msg);
        }
	}
    

    public function init() {
        $this->_keys = Zend_Registry::get('keys');
    }

    public function indexAction() {
       	$auth = Zend_Auth::getInstance();
		return $this->_redirect()->gotoRoute(array(),'login');
    }
    
    public function loginAction(){
    	
    	$redirect = $this->getRequest()->getParam('redirect',null);
    	$resource = $this->getRequest()->getParam('resource',null);
    	$message = $this->getRequest()->getParam('message',null);
    	$this->view->form = $this->getLoginForm();
    	
    	$this->log('Logging in...');
    	
    	$authSess = new Zend_Session_Namespace('Auth');
    	if($redirect){
    		$this->view->form->redirect->setValue($redirect);
    		$authSess->redirect = $redirect;
    	}
    	if($resource){
    	    $authSess->resource = urldecode($resource);
    	}
    	if($message){
    	    switch($message){
    	        case 'logintoedit':
    	            $this->view->message = 'You must be logged in to edit this page.';
    	        break;
    	    }
    	}
    }

	public function authenticateAction(){
        $this->log('Local auth');
		$request = $this->getRequest();
		 
        // Check if we have a POST request
        if (!$request->isPost())
            return $this->_helper->redirector()->gotoRoute(array(),'login');
 
        // Get form and validate
        $form = $this->getLoginForm();
        if (!$form->isValid($request->getPost())) {
            // Did not pass validation...
            $this->view->form = $form;
            return $this->render('login');
        }
 		
        // Get authentication adapter and check credentials
        $adapter = $this->_getLocalAdapter($form->getValues());
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            $this->log('Invalid login');
            // Invalid username/password
            if($result->code < Zend_Auth_Result::FAILURE_UNCATEGORIZED){
                if($result->code === My_Auth_Adapter_Local::FAILURE_CREDENTIAL_UNCONFIRMED){
                    return $this->_redirect('account/unconfirmed');
                }
            }
            $messages = $result->getMessages();
            $form->setDescription($messages[0]);
            $this->view->form = $form;
            return $this->render('login');
        }
        
        $this->log('Valid login: '.$auth->getIdentity());
        $users = new Application_Model_Users();
        $user = $users->getUserBy('email',$auth->getIdentity());
        
        //set auth session data 	
 		$authSess = new Zend_Session_Namespace('Auth');
 		$authSess->identity = $auth->getIdentity();
 		$authSess->user = $user->toArray();
 		
 		$persist = ((int) $form->getValue('persist')==1) ? true : false;
 		
 		//echo (int) $form->getValue('persist'); exit;
 		
 		//prepare integrated services 
		$integrations = new Application_Model_Integrations(array('persist'=>$persist));
		$integrations->onAuthenticate();

        $post = $request->getPost();
        if(isset($post['redirect']) && !empty($post['redirect'])){
        	//whitelist domain here?
        	$this->view->redirect = $post['redirect'];
        	if(isset($authSess->resource)){
        	    $this->view->resource = $authSess->resource;
        	    unset($authSess->resource);
        	}
        	return $this->render('redirect');
        	//$this->_forward('redirect');
        	//header('Location: '.$post['redirect']); 
        	//exit;
        	//no idea why the zend redirector doesnt work but meh
        	//$this->_helper->redirector()->gotoUrl($post['redirect']);
        	//exit;
        }
       $this->_helper->redirector()->gotoRoute(array(),'home');
       
	}
	
    public function authenticateExternalAction() {
        // get an instace of Zend_Auth
        $auth = Zend_Auth::getInstance();
        $authSess = new Zend_Session_Namespace('Auth');

        // check if a user is already logged
        if ($auth->hasIdentity()) {
            $this->_helper->FlashMessenger('You are already logged in.');
            return $this->_helper->redirector()->gotoRoute(array(),'home');
        }

        // if the user is not logged, the do the logging
        // $openid_identifier will be set when users 'clicks' on the account provider
        $openid_identifier = $this->getRequest()->getParam('openid_identifier', null);
        if($openid_identifier)
        	$authSess->identifier = $openid_identifier;
        //echo 'ID: '.$openid_identifier.'<br>'; exit;
        
        
        
        // $openid_mode will be set after first query to the openid provider
        $openid_mode = $this->getRequest()->getParam('openid_mode', null);

        // this one will be set by facebook connect
        $code = $this->getRequest()->getParam('code', null);

        // while this one will be set by twitter
        $oauth_token = $this->getRequest()->getParam('oauth_token', null);

        // do the first query to an authentication provider
        if ($openid_identifier) {
            $this->log('Authenticating using external source: '.$openid_identifier);
            
            if ('https://www.twitter.com' == $openid_identifier) {
                $adapter = $this->_getTwitterAdapter();
            } else if ('https://www.facebook.com' == $openid_identifier) {
                $adapter = $this->_getFacebookAdapter();
            } else {
                // for openid
                $adapter = $this->_getOpenIdAdapter($openid_identifier);

                // specify what to grab from the provider and what extension to use
                // for this purpose
                $toFetch = $this->_keys->openid->tofetch->toArray();
                // for google and yahoo use AtributeExchange Extension
                if ('https://www.google.com/accounts/o8/id' == $openid_identifier || 'http://me.yahoo.com/' == $openid_identifier) {
                    $ext = $this->_getOpenIdExt('ax', $toFetch);
                } else {
                    $ext = $this->_getOpenIdExt('sreg', $toFetch);
                }

                $adapter->setExtensions($ext);
            }

            // here a user is redirect to the provider for loging
            $result = $auth->authenticate($adapter);

            $this->log('Login redirection failed.');
            $this->log(print_r($result, true));
            // the following two lines should never be executed unless the redirection faild.
            //$this->_helper->FlashMessenger('Redirection failed');
            //$this->_redirect('/login');
            $form = $this->getLoginForm();
            $this->view->form = $form;
            $form->addError('Something went wrong while authenticating you! Please email help@practicalplants.org for assistance.');
            return $this->render('login');
            
            
        } else if ($openid_mode || $code || $oauth_token) {
        
            
            
            // this will be exectued after provider redirected the user back to us
            
            if ($code) {
              $this->log('Facebook response: '.$code);
                // for facebook
                $adapter = $this->_getFacebookAdapter();
            } else if ($oauth_token) {
              $this->log('Twitter  response: '.$oauth_token);
                // for twitter
                $adapter = $this->_getTwitterAdapter();
            } else {
                // for openid                
                $adapter = $this->_getOpenIdAdapter(null);

                // specify what to grab from the provider and what extension to use
                // for this purpose
                $ext = null;
                
                $toFetch = $this->_keys->openid->tofetch->toArray();
                $this->log('OpenID response: '.$toFetch);
                // for google and yahoo use AtributeExchange Extension
                if (isset($_GET['openid_ns_ext1']) || isset($_GET['openid_ns_ax'])) {
                    $ext = $this->_getOpenIdExt('ax', $toFetch);
                } else if (isset($_GET['openid_ns_sreg'])) {
                    $ext = $this->_getOpenIdExt('sreg', $toFetch);
                }

                if ($ext) {
                    $ext->parseResponse($_GET);
                    $adapter->setExtensions($ext);
                }
            }

            $result = $auth->authenticate($adapter);
            $this->log('Auth result: '.(string) $result->isValid()); 
            if ($result->isValid()) {
                $externalData = array('identity' => $auth->getIdentity());
                $this->log('Auth identity: '.print_r($auth->getIdentity(), true));
                if (isset($ext)) {
                    // for openId
                    $externalData['properties'] = $ext->getProperties();
                    $externalData['provider'] = 'OpenID';
                } else if ($code) {
                    // for facebook
                    $msgs = $result->getMessages();
                    $externalData['properties'] = (array) $msgs['user'];
                    $externalData['provider'] = 'Facebook';
                } else if ($oauth_token) {
                    // for twitter
                    $identity = $result->getIdentity();
                    // get user info
                    $twitterUserData = (array) $adapter->verifyCredentials();
                    $externalData = array('identity' => $identity['user_id']);

                    $externalData['properties'] = $twitterUserData;
                    $externalData['provider'] = 'Twitter';
                }else{
                	//we don't have external data!
                	$externalData['provider'] = 'OpenID';
                }
                $this->log('External auth valid: '.$externalData);

                
                /* Process after external auth is valid:
                	- See if there's already a local user which matches the auth method & identity
                	- If not, make sure the auth provider gave us an email address
                	- Check to see if there are any local users with that email address
                	- If so, then inform the user if they'd like to associate the two accounts, they can do so by entering the local password
                	- If not, register the user a local account with the email address from the external auth, and allow them to choose a username and display name, prefilling the data where provided from the external auth
                */
                //print_r($ext);
                //print_r($externalData);
				//print_r($authSess->identifier);
                //print_r($openid_mode);
                //exit;
                
                if(!is_array($externalData) || !isset($externalData['properties'])){// || !is_array($externalData['properties']) || !isset($externalData['properties']['email'])){
                	//$auth->clearIdentity();
                	//print_r($externalData); exit;
                	//$this->view->external_provider = $externalData['provider'];
                	
                	//return $this->render('authenticate-external-insufficient');
                }else{
					//$auth->getStorage()->write($externalData);
					$authSess->external = $externalData;
                }
                
                $users = new Application_Model_Users();
                
                //print_r($externalData); exit;
				
				//check if a user can be found associated with this external identity
                if(! $user = $users->getUserByAuthIdentity($externalData['provider'], $externalData['identity'] ) ){
                
                	//store auth data to session
                	/*$sess = new Zend_Session_Namespace('CreateUserFromExternal');
                	$sess->provider = $externalData['provider'];
                	$sess->identity = $externalData['identity'];
                	$auth->clearIdentity();*/
                	
                	//echo 'no auth user - ';
                	//echo 'No local user'; exit;
                	//no local user attached to this auth provider
                	if(isset($externalData['properties']['email']))
                		$user = $users->getUserBy('email',$externalData['properties']['email']);
                	if(isset($user) && $user!==false){
            			//local user with matching email, but not logged in with this auth before, attempt to link!
            			//$auth->getStorage()->write($user->email); //change identity to email address
            			$user->associateNewProvider();
                        return $this->_forward('associate-provider');
            			//echo 'found email user';
                	}else{
                		//no user found, redirect to form to create new local user
                		//$this->_redirect()->gotoRoute(array(),'create-from-external');
                		//clear identity - user has been successfully externally authenticated, but no local user account exists yet
                		$auth->clearIdentity();
                		return $this->_forward('create-from-external');
                	}
                	
                	
                }else{
                	$authSess = new Zend_Session_Namespace('Auth');
                	//echo 'Local authed';print_r($user->toArray()); exit;
                	//user has authenticated externally and has local account, so change identity to local
                	$auth->getStorage()->write($user->email); 
                	$authSess->identity = $auth->getIdentity();
                	$authSess->user = $user->toArray();
                	//echo 'DONE'; exit;
                	//tell integrated services the user is logged in
                	$integrations = new Application_Model_Integrations();
                	$integrations->onAuthenticate();
                	
                	if(isset($authSess->redirect) && !empty($authSess->redirect)){
                	 	//whitelist domain here?
                	 	$this->view->redirect = $authSess->redirect;
                	 	return $this->render('redirect');
                	 	//header('Location: '.$authSess->redirect); 
                	 	//exit;
                	 	//$this->_helper->redirector()->gotoUrl($authSess->redirect);
                	 }
                	$this->_helper->redirector()->gotoRoute(array(),'home');
                
                }
                              
            } else {
            	//echo 'Failed auth<pre>';
            	//print_r($result);
            	//exit;
                //$this->_helper->FlashMessenger('Failed authentication');
                //$this->_helper->FlashMessenger($result->getMessages());
                $this->view->message = implode(' - ',$result->getMessages()) || 'Authentication failed.';
                $this->log( 'External auth failed: '.print_r($result->getMessages(), true) );
                return $this->render('error');
            }
        }else{
            $this->view->message = 'No OpenID authentication URL provided.';
            return $this->render('error');
        }
    }
    
    public function createFromExternalAction(){
    
    	//@todo check if the user isn't already logged in with a local account - redirect to account home if so
    	
        $auth = Zend_Auth::getInstance();
        $authSess = new Zend_Session_Namespace('Auth');
        $identity = $auth->getIdentity();
        $external = $authSess->external;
        $users = new Application_Model_Users();
       	
        //if(!isset($authData['identity']) || !isset($authData['properties']) || !isset($authData['properties']['email']) || !isset($authData['provider']))
        //	throw Exception('Invalid auth data');
        $form = new Application_Form_RegisterFromExternal();
        $form->setAction($this->_helper->url('create-from-external'));
        if($this->getRequest()->isPost() ){
        	if (!$form->isValid($this->getRequest()->getPost())) {
        	    // Did not pass validation...
        	    $this->view->form = $form;
        	    return $this->render();
        	}
        	$values = $form->getValues(); /*array_merge(
        		$form->getValues(),
        		array(
        			'email'=>$external['properties']['email']
        		)
        	);*/

        	//if(!isset($values['email']) || !isset($values['username']))
        	  //  throw new Exception('Email or username not supplied');
	    	if(!isset($external['provider']) || !isset($external['identity']))
	    		throw new Exception('Email or username not supplied');
	    	
	    	//check for local with matching email
	    	if(!$user = $users->getUserBy('email',$values['email'])){
	    		//no local user with matching email, create new local user
	
	    		try{
	    			//create new user 
	    			$user = $users->createUser($values); 

                    //email user their password
                    if(isset($user->email)){
                        $email = $user->email;
                        $email_to = $user->display_name ?: $user->username;
                        $password = $values['password'];
                        $subject = $email_to.', you logged in to Practical Plants';
                        
                        $html = '<h1>Hello '.$email_to.', thanks for logging in to Practical Plants</h1>' 
                              . '<p>You logged in using '.$external['provider'].'</p>'
                              . '<p>Next time you log in, either use '.$external['provider'].', or you can login using the account details you just chose: </p>'
                              . '<blockquote><p>Email: '. $email .'</p>'
                              . '<p>Password: ' . $password . '</p></blockquote>'
                              . '<p>If you have any questions, drop by the <a href="http://practicalplants.org/community">Community Forums</a> or email us: hello@practicalplants.org</p>';
                        $text = "Hello $email_to, thanks for logging in to Practical Plants\n"
                                . "You logged in using $external[provider].\n"
                                . "Next time you log in, either use $external[provider], or you can login using the account details you just chose: \n"
                                . "\tEmail: $email\n"
                                . "\tPassword: $password\n"
                               . "If you have any questions, drop by the Community Forums (http://practicalplants.org/community) or email us: hello@practicalplants.org";


                        $this->sendMail($email_to, $email, $html, $text, $subject);
                    }
	    			$users->setUserActive($user); //set email confirmed and active, we trust the external authentication service
	    			$auths = new Application_Model_User_Authentications();
	    			//associate the users external authentication with the local user
	    			$auths->addAuthentication($user,$external['provider'],$external['identity']);
	    			//add local user to session
	    			$authSess->user = $user->toArray();
	    			//update auth identity to email address (used for local auth)
	    			$auth->getStorage()->write($user->email);
	    			
	    			//tell integrated services we've logged in
	    			$integrations = new Application_Model_Integrations();
	    			$integrations->onAuthenticate();
	    			
	    			//show success message
	    			$this->view->external_provider = $authSess->external['provider'];
	    			return $this->render('external-user-created');
	    		}catch(Exception $e){
	    			//print_r($values);
	    			if(!$error = $e->getMessage())
	    				$error = 'Sorry, an error occurred while creating your account. Please try again.';
	    			$this->view->message = $error;
	    			return $this->render('error');
	    		}
	    	}else{
	    		$this->view->message = 'Cannot create user. User with the email address '.$values['email'].' already exists.';
	    		return $this->render('error');
	    	}
        	    	
        }else{
        	if(isset($authSess->external)){
	        	$props = $authSess->external['properties'];
	        	if(isset($props['name']))
	        		$props['display_name'] = $props['name'];
	        	if(isset($props['screen_name']))
	        		$props['username'] = $props['screen_name'];
	        	$fields = array_keys($form->getValues());
	        	$populate = array();
	        	foreach($props as $k => $v){
	        		if(in_array($k,$fields)){
	        			$populate[$k]=$v;
	        		}
	        	}
	        	$form->populate($populate);
	        	$this->view->form = $form;
	        }else{
	        	throw Exception('No external authentication data.');
	        }
        }
    }
    
    
    /**
     * Sends an email
     * 
     * @param string $html
     * @param string $text
     * @param string $title
     * @return void
     */
    protected function sendMail($name, $email, $html, $text, $title)
    {   
        $options = Zend_Registry::get('options');
        $from = $options['email']['from'];
        $from_name = $options['email']['from_name'];
        $mail = new Zend_Mail();
        $mail->setBodyText($text);
        $mail->setBodyHtml($html);
        $mail->setFrom($from, $from_name);
        $mail->addTo($email);
        $mail->setSubject($title);
        $mail->send();
    }

    public function associateProviderAction(){
    	//not implemented yet, just log user out and display a message telling them to use their original method
    	$auth = Zend_Auth::getInstance();
    	$auth->clearIdentity();
    	$this->render('associate-new-provider');
    }
    



    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        
        $this->destroySession();
        //$this->_helper->FlashMessenger('You were logged out');
        return $this->_redirect('/goodbye');
    }
    
    protected function destroySession(){
        $integrations = new Application_Model_Integrations();
        $integrations->onDestroySession();
        
    	if (Zend_Session::sessionExists()) {
    	    Zend_Session::destroy(true, true);
    	}
    	
    }

    /**
     * Get My_Auth_Adapter_Facebook adapter
     *
     * @return My_Auth_Adapter_Facebook
     */
    protected function _getFacebookAdapter() {
        extract($this->_keys->facebook->toArray());
        return new My_Auth_Adapter_Facebook($appid, $secret, $redirecturi, $scope);
    }

    /**
     * Get My_Auth_Adapter_Oauth_Twitter adapter
     *
     * @return My_Auth_Adapter_Oauth_Twitter
     */
    protected function _getTwitterAdapter() {
        /*extract($this->_keys->twitter->toArray());
        $this->log('Instantiating Twitter Oauth adapter...');
        $this->log('Appid: '.$appid);
        $this->log('Secret: '.$secret);
        $this->log('Redirecturl: '.$redirecturi);
        return new My_Auth_Adapter_Oauth_Twitter(array(), $appid, $secret, $redirecturi);*/
        extract($this->_keys->twitter->toArray());
        return new My_Auth_Adapter_Twitter($appid, $secret, $redirecturi);
    }

    /**
     * Get Zend_Auth_Adapter_OpenId adapter
     *
     * @param string $openid_identifier
     * @return Zend_Auth_Adapter_OpenId
     */
    protected function _getOpenIdAdapter($openid_identifier = null) {
        $adapter = new Zend_Auth_Adapter_OpenId($openid_identifier);
        $dir = APPLICATION_PATH . '/../private/tmp';

        if (!file_exists($dir)) {
            if (!mkdir($dir)) {
                throw new Zend_Exception("Cannot create $dir to store tmp auth data.");
            }
        }
        $adapter->setStorage(new Zend_OpenId_Consumer_Storage_File($dir));

        return $adapter;
    }

    /**
     * Get Zend_OpenId_Extension. Sreg or Ax. 
     * 
     * @param string $extType Possible values: 'sreg' or 'ax'
     * @param array $propertiesToRequest
     * @return Zend_OpenId_Extension|null
     */
    protected function _getOpenIdExt($extType, array $propertiesToRequest) {

        $ext = null;

        if ('ax' == $extType) {
            $ext = new My_OpenId_Extension_AttributeExchange($propertiesToRequest);
        } elseif ('sreg' == $extType) {
            $ext = new Zend_OpenId_Extension_Sreg($propertiesToRequest);
        }

        return $ext;
    }
	
	/**
	 * Get PasswordHashAuth_Auth_Adapter Sreg or Ax. 
	 * 
	 * @param array $params Login details (username=>, password=>)
	 * @return Zend_OpenId_Extension|null
	 */
	public function _getLocalAdapter(array $params) {
	    return new My_Auth_Adapter_Local(
	        $params['email'],
	        $params['password'],
	        new Application_Model_Users()
	    );
	}
	
	
	public function getLoginForm() {
	    return new Application_Form_Login(array(
	        'action' => '/sso/authenticate/',
	        'method' => 'post'
	    ));
	}
}

