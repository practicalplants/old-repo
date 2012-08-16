<?php

class IntegrationController extends Zend_Controller_Action {

	public function init(){
		$this->integrations = new Application_Model_Integrations();
	}

	public function indexAction() {
	
	
	}
	
	public function shareSessionAction() {
		$this->_helper->layout->disableLayout();
		
		if(!Zend_Auth::getInstance()->hasIdentity()){
			return $this->render(); 
		}
		
		$authSess = new Zend_Session_Namespace('Auth');
		
		$params = $this->getRequest()->getParams();
		$app = isset($params['application']) ? $params['application'] : null;
		
		$user = $authSess->user;
		
		switch($params['application']){

			case 'vanilla':
				if(Zend_Auth::getInstance()->hasIdentity()){
					$identity = Zend_Auth::getInstance()->getIdentity();
					//$users = new Application_Model_Users();
					//$user = $users->getUserBy('email',$email);
					
					$this->view->data = array(
						'UniqueID'=>$user['user_id'],
						'Name'=>$user['username'],
						'Email'=>$user['email']
					);
					
					$this->render('share-session-vanilla');
					
					/*
					TransientKey=02742kjd2820
					DateOfBirth=1970-01-01
					Gender=Male*/
				}
				break;
			
			case 'json':
			case 'wordpress':
			case 'mediawiki':
			default:
				
				
				//$users = new Application_Model_Users();
				//$user = $users->getUserBy('email',$email);
				
				$this->view->data = json_encode(array(
					'id'=>$user['user_id'],
					'username'=>$user['username'],
					'email'=>$user['email'] 
				));
				
				$this->render('share-session');
					
				
				break;
		}
		
		
	}
	
	public function destroySessionAction(){
		$this->_redirect()->gotoRoute(array(),'logout');
	}


	public function authenticateAction(){
		if(!$this->getRequest()->isPost())
			return false;
		$post = $this->getRequest()->getPost();
		if(isset($post['email']) && isset($post['password'])){
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate(
				new My_Auth_Adapter_Local(
				    $post['email'],
				    $post['password'],
				    new Application_Model_Users()
				)
			);
			$this->view->result = $result->isValid();
		}
		$this->render('boolean-result');
	}
	
	public function usernameExistsAction(){
		if( $username = $this->getRequest()->getParam('username') ){	
			$users = new Application_Model_Users();
			$user = $users->getUserBy('username',$username);
			if($user){
				$result = true;
			}
		}
		$this->view->result = isset($result) ? $result : false;
		$this->render('boolean-result');
	}
	
	public function emailExistsAction(){
		if( $email = $this->getRequest()->getParam('email') ){	
			$users = new Application_Model_Users();
			$user = $users->getUserBy('email',$email);
			if($user){
				$result = true;
			}
		}
		$this->view->result = isset($result) ? $result : false;
		$this->render('boolean-result');
	}

}