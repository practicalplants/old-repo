<?php
/**
 * Short description for file
 *
 * Long description for file (if any)...
 *
 * LICENSE: Some license information
 *
 * @category   Zend
 * @package    Zend_
 * @subpackage Wand
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license   BSD License
 * @version    $Id:$
 * @link       http://framework.zend.com/package/PackageName
 * @since      File available since Release 1.5.0
 */
class AccountController extends Zend_Controller_Action
{
    /**
     * Default minimum number of characters for a password
     */
    const MIN_PASS_CHAR = 6;
    
    /**
     * Default maximum number of characters for a password
     */
    const MAX_PASS_CHAR = 75;
    
    /**
     * Default maximum number of characters for username and email
     */
    const MAX_EMAIL_USERNAME_CHAR = 255;

    /**
     * Base URL
     */
    private $_baseURL = '';

	public function init(){
		$bootstrap = $this->getInvokeArg('bootstrap'); 
		 $options = $bootstrap->getOptions();
		 $this->_baseURL = $options['app']['url'];
	}	

    /**
     * Controller's entry point
     *
     * @return void
     */
    public function indexAction()
    {
        /*$this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        $session = new Zend_Session_Namespace();
        $this->view->flashMessengerClass = $session->flashMessengerClass;*/
        
        /*$params = $this->getRequest()->getParams();
        $sess = Zend_Session_Namespace('SSO');
        if(isset($sess->login_redirect)){
        
        }
        $this->view->form = $this->getForm();
        if(isset($params['redirect'])){
        	$this->view->form->redirect->setValue($params['redirect']);
        }*/
        
        //$this->_helper->redirector()->gotoRoute(array(),'home');
		$this->_forward('index','index');
	}
	
    
    /**
     * Handles the register action which displays the registration form
     *
     * @return void
     */
    public function registerAction()
    {
        //if a user session is currently still active, ask them to logout?
        
        $session = new Zend_Session_Namespace('registration');
        //if( isset( $session->usernameRegistration ) )
         //   $this->view->usernameRegistration = $session->username;
        //if( isset( $session->emailRegistration ) )
           // $this->view->emailRegistration = $session->email;
           
        $this->view->form = $this->getRegisterForm();
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->view->flashMessengerClass = $session->flashMessengerClass;
     //   Zend_Session::destroy();
    }
    
    /**
     * Handles the registration of the user, validating the user input,
     * inserting into the database, and sending the email activation, 
     *
     * @return void
     */
    public function registrationAction()
    {
        if($this->getRequest()->isPost() ) {
            
            $form = $this->getRegisterForm();
           // if($form->getValue('password') !== $form->getValue('password-conf'))
            //	$form->addError("Passwords do not match!");
            if (!$form->isValid($this->getRequest()->getPost())) {
                // Did not pass validation...
                $this->view->form = $form;
                return $this->render('register');
            }
            
            

            $users = new Application_Model_Users();           
            
            try{
            	$user = $users->createUser($form->getValues());           	
            	
            }catch(Exception $e){
            	if(!$error = $e->getMessage())
            		$error = 'Sorry, an error occurred while creating your account. Please try again.';
            	$this->view->messages = array($error);
            	//$form->markAsError();
            	//$form->isValid($this->getRequest()->getPost());
            	$this->view->form = $form;
            	return $this->render('register');
            }
          	
          	//print_r((array) $user->toArray());exit;
            
            if($user){
            	$this->sendActivationEmail($user);
            	
            	//return $this->_redirect()->gotoRoute(array(),'registered');
            	return $this->_forward('registered');
            }
            
        }
        $this->_redirect()->gotoRoute(array(),'register');
    }
    
    /**
     * Success or error page in registration process
     *
     * @return void
     */
    public function registeredAction()
    {
        
    }
    
    /**
     * Handles the activation of a new user account
     * 
     * @return void
     */
    public function activateAction()
    {
    	
        $id = $this->getRequest()->getParam('id');
        $url_code = $this->getRequest()->getParam('code');
        
        $users = new Application_Model_Users();
        if($user = $users->getUserBy('id',$id)){
        	if(substr(md5($user->register_time.$user->register_ip),10,5) === $url_code)
        		$result = $users->activateUser($id,$url_code);
        }else{
        	$result = false;
        }
        
        
        
        if(!$result) {
            $this->render('activation-failure');
        } else {
        
        	//log user in
        	$auth = Zend_Auth::getInstance();
        	$auth->getStorage()->write($user->email);
        	
        	$authSess = new Zend_Session_Namespace('Auth');
        	$authSess->identity = $user->email;
        	$authSess->user = $user->toArray();
        	
        	$integrations = new Application_Model_Integrations();
        	$integrations->onAuthenticate();
        	
            $this->render('activation-success');
        }
    }


    public function unconfirmedAction(){
        $this->render('awaiting-activation');
    }

    public function resendActivationAction(){
        $form = new Application_Form_ResendActivation(array(
            'action' => '/sso/account/resend-activation',
            'method' => 'post'
        ));
        $this->view->form = $form;
        if($this->getRequest()->isPost() ) {

            if (!$form->isValid($this->getRequest()->getPost())) {
                // Did not pass validation...
                $this->view->form = $form;
                return $this->render('resend-activation');
            }
            
            $users = new Application_Model_Users();           
            
            try{
                $user = $users->getUserBy('email',$form->getValue('email'));               
            }catch(Exception $e){
                if(!$error = $e->getMessage())
                    $error = 'Sorry, an error occurred while finding your account. Please try again.';
                $this->view->messages = array($error);
                return $this->render('resend-activation');
            }
            
            //print_r((array) $user->toArray());exit;
            
            if($user){
                if($user->email_confirmed){
                    $form->addError('Your account has already been activated.');
                }else{
                   $this->sendActivationEmail($user);
                    return $this->render('resent-activation'); 
                }
            }else{
                $form->addError('There is no account with this email address.');
            }
            return $this->render('resend-activation');
        }else{
            $this->render('resend-activation');  
        }
    }
    
    /**
     * Displays the form for the user to reset their password
     *
     * @return void
     */
    public function forgotPasswordAction() 
    {
        //simply displays messages returned from activateAction()
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $session = new Zend_Session_Namespace();
        $this->view->flashMessengerClass = $session->flashMessengerClass;
    }
    
    /**
     * Emails user URL to change password
     *
     * @return void
     */
    public function forgotPasswordProcessAction() 
    {
        if( $this->getRequest()->isPost() ) {
            $email = $this->getRequest()->getPost('email');
            
            //recovery only active for three days
            $date = date('YYYY-MM-DD');
            $date = strtotime ( '+3 day' , strtotime ( $date ) ) ;
            $date = date ( 'YYYY-MM-DD' , $date );
            
            $user = new Model_DbTable_Users();
            $result = $user->emailExists($email);
            if( $result != false ) {
                $guid = uniqid();
                $reset = new Model_DbTable_PasswordReset();
                $result = $reset->insert( array( 'guid' => $guid, 'id' => $result, 'expiry_date' => $date ) );
                if( !is_array($result) ) {
                    $html = "<p>To reset your password, click <a href=\"".$this->_baseURL."/resetpass/id/$guid\">here</a>.</p>";
                    $text = "Go to the following link to reset your password ".$this->_baseURL."/resetpass/id/$guid\n";
                    sendMail($username, $email, $html, $text, 'Password Reset');
                    $session = new Zend_Session_Namespace();
                    $session->flashMessengerClass = 'flashMessagesGreen';
                    $this->_helper->flashMessenger->addMessage('An email has been sent to you with instructions on how to reset your password.');
                } 
            } else { //return with error
                $session = new Zend_Session_Namespace();
                $session->flashMessengerClass = 'flashMessagesRed';
                $this->_helper->flashMessenger->addMessage('We have no record of that email address.');
            }
        }
        $this->_redirect->gotoRoute(array(),'forgot-password');
    }
    
    /**
     * Asks the user 
     *
     * @return void
     */
    public function resetpassAction()
    {
        if( strlen( ($this->getRequest()->getParam('id')) ) == 0 ) {//if password empty, return back
            $this->_redirect('/login/forgotpassword/');
        } else {//success changed password
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $session = new Zend_Session_Namespace();
            $this->view->flashMessengerClass = $session->flashMessengerClass;
            $this->view->guid = $this->getRequest()->getParam('id');
        }
    }
    
    /**
     * Processes the new password and stores in DB
     *
     * @return void
     */
    public function resetpassprocessAction()
    {
        if( $this->getRequest()->isPost() ) {
            $password = $this->getRequest()->getPost('password');
            $passwordConfirm = $this->getRequest()->getPost('passwordConfirm');
            $guid = $this->getRequest()->getPost('guid');
            
            //check valid password
            $passwordLengthValidator = new Zend_Validate_StringLength(array('min' => MIN_PASS_CHAR, 'max' => MAX_PASS_CHAR));
            $alNumValidator = new Zend_Validate_Alnum();
            
            $error = false;
            if( strcmp($password, $passwordConfirm) != 0 ) {
                $this->_helper->flashMessenger->addMessage('Your passwords do not match.');
                $error = true;
            }
            if( !$passwordLengthValidator->isValid($password) ) {
            
                if( !$alNumValidator->isValid($password) ) {
                    $this->_helper->flashMessenger->addMessage('You password must only consist of letters and numbers.');
                    $error = true;
                } else {
                    $this->_helper->flashMessenger->addMessage('Passwords must be between ' . MIN_PASS_CHAR . ' and ' . MAX_CHAR_PASS . ' characters in length.');
                    $error = true;
                }
            }
            
            //if validation errors, store data in view
            if($error) {
                $session = new Zend_Session_Namespace();
                $session->flashMessengerClass = 'flashMessagesRed';
                $session->guid = $guid;
                $this->_redirect('/login/resetpass/id/' . $guid . '/');
            } else {
                //register use and redirect to success page
                $options= $this->getInvokeArg('bootstrap')->getOptions();
                $salt = $options['password']['salt'];
                $user = new Model_DbTable_Users();
                $passwordReset = new Model_DbTable_PasswordReset();
                $id = $passwordReset->getID($guid);
                $result = $user->changePassword($id, sha1($password . $salt));
                $username = $user->getUsername($id);
                $email = $user->getEmail($id);
                if( $result != null ) {
                    $passwordReset->delete($passwordReset->getAdapter()->quoteInto('guid = ?', $guid));
                    //send email with username and password.
                    $html = '<p>Your new login information is below:</p>'
                          . '<p>Username: ' . $username . '</p>'
                          . '<p>Password: ' . $password . '</p>';
                    $text = "Your new login information is below:\n"
                          . "Username: $username . \nPassword: $password \n";
                    $this->sendMail($username, $email, $html, $text, 'Account Information');
                    $session = new Zend_Session_Namespace();
                    $session->flashMessengerClass = 'flashMessagesGreen';
                    $this->_helper->flashMessenger->addMessage('Your password has been successfully reset.');
                    $this->_redirect('/login/index/');
                } else {
                    $session = new Zend_Session_Namespace();
                    $session->flashMessengerClass = 'flashMessagesRed';
                    $this->_helper->flashMessenger->addMessage('Your password could not be reset.');
                    $this->_helper->redirector->gotoRoute(array(),'forgot-password');
                }
            }
        } else {
            $this->_helper->redirector->gotoRoute(array(),'forgot-password');
        }
    }


    protected function sendActivationEmail(Zend_Db_Table_Row $user, $password = null){
        //$username = $form->getProperty('username');
        if(!isset($user->email))
            throw 'Invalid user object for sendActivationEmail';
        $email = $user->email;
        $email_to = $user->display_name ?: $user->username;
        
        $hash = substr(md5($user->register_time.$user->register_ip),10,5);
        
        //echo $hash;exit;
        $url = $this->_baseURL.'/activate/'.$user->user_id.'/'.$hash;
        $html = '<h1>Your account at Practical Plants</h1>' 
              . '<p><a href="'.$url.'">Click here</a> or enter this url into your browser to activate your account: '
              . $url.'</p>'
              . '<p>Your password is: ' . $password . '</p>';
        $text = "Activate your account on Practical Plants here: "
              . $url." \n";

        //add password to activation email on initial registration - we can only do this when the form has just been
        //submitted. After that it's cryped.
        if($password!==null){
            $text .= "Your password is: $password\n";
        }

        $this->sendMail($email_to, $email, $html, $text, 'Account Activation');
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
    
    protected function getRegisterForm() {
        return new Application_Form_Register(array(
            'action' => '/sso/registration',
            'method' => 'post'
        ));
    }

}