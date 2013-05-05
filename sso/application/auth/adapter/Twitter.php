<?php

/**
 * Twitter login using twitteroauth library instead of the flaky Zend one
 *
 * @link http://www.krotscheck.net/2010/08/21/zend_auth_adapter_facebook.html
 * @author Michael Krotscheck
 */
class My_Auth_Adapter_Twitter implements Zend_Auth_Adapter_Interface
{
    /**
     * The Authentication URI, used to bounce the user to the facebook redirect uri.
     *
     * @var string
     */
    const AUTH_URI = 'https://';

    /**
     * The token URI, used to retrieve the OAuth Token.
     *
     * @var string
     */
    const TOKEN_URI = 'https://api.twitter.com/oauth/request_token';

    const AUTHORIZE_URI = 'https://api.twitter.com/oauth/authorize';

    const ACCESS_TOKEN_URI = 'https://api.twitter.com/oauth/access_token';

    /**
     * The application ID
     *
     * @var string
     */
    private $_appId = null;

    /**
     * The application secret
     *
     * @var string
     */
    private $_secret = null;

    /**
     * The redirect uri
     *
     * @var string
     */
    private $_redirectUri = null;

    /**
     * Constructor
     *
     * @param string $appId the application ID
     * @param string $secret the application secret
     * @param string $scope the application scope
     * @param string $redirectUri the URI to redirect the user to after successful authentication
     */
    public function __construct($appId, $secret, $redirectUri)
    {
        $this->_appId = $appId;
        $this->_secret = $secret;
        $this->_redirectUri   = $redirectUri;

        if(isset($_SESSION['access_token'])){
          $this->access_token = $_SESSION['access_token'];
        }
    }

    /**
     * Sets the value to be used as the application ID
     *
     * @param  string $appId The application ID
     * @return My_Auth_Adapter_Facebook Provides a fluent interface
     */
    public function setAppId($appId)
    {
        $this->_appId = $id;
        return $this;
    }

    /**
     * Sets the value to be used as the application secret
     *
     * @param  string $secret The application secret
     * @return My_Auth_Adapter_Facebook Provides a fluent interface
     */
    public function setSecret($secret)
    {
        $this->_secret = $secret;
        return $this;
    }


    /**
     * Sets the redirect uri after successful authentication
     *
     * @param  string $redirectUri The redirect URI
     * @return My_Auth_Adapter_Facebook Provides a fluent interface
     */
    public function setRedirectUri($redirectUri)
    {
        $this->_redirectUri = $redirectUri;
        return $this;
    }


    /**
     * Authenticates the user against facebook
     * Defined by Zend_Auth_Adapter_Interface.
     *
     * @throws Zend_Auth_Adapter_Exception If answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
      // Get the request object.
      $frontController = Zend_Controller_Front::getInstance();
      $request = $frontController->getRequest();

      // First check to see wether we're processing a redirect response.
      $oauth_token = $request->getParam('oauth_token', null);

      if ( empty ($oauth_token ) ){
        return $this->redirectToTwitter();
      }else{
        return $this->handleCallback($oauth_token);
      }
    }


    protected function redirectToTwitter(){
      require(BASE_PATH.'/library/twitteroauth/twitteroauth/twitteroauth.php');

      /* Build TwitterOAuth object with client credentials. */
      $connection = new TwitterOAuth($this->_appId, $this->_secret);
       
      /* Get temporary credentials. */
      $request_token = $connection->getRequestToken($this->_redirectUri);

      /* Save temporary credentials to session. */
      $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
      $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
       
      /* If last connection failed don't display authorization link. */
      switch ($connection->http_code) {
        case 200:
          /* Build authorize URL and redirect user to Twitter. */
          $url = $connection->getAuthorizeURL($token);
          header('Location: ' . $url);
          exit;
          break;
        default:
          /* Show notification if something went wrong. */
          return new Zend_Auth_Result( Zend_Auth_Result::FAILURE, null, array('Error while attempting to redirect.') );
      }
    }

    protected function handleCallback($oauth_token){
      if(isset($this->access_token)){
        return new Zend_Auth_Result( Zend_Auth_Result::SUCCESS, $this->access_token['user_id'], array('user'=>array('id'=>$this->access_token['user_id'], 'screen_name'=>$this->access_token['screen_name']), 'token'=>$this->access_token) );
      }
      if(!isset($_SESSION['oauth_token'])){
        return $this->redirectToTwitter();
      }
      require_once(BASE_PATH.'/library/twitteroauth/twitteroauth/twitteroauth.php');

      /* Create TwitteroAuth object with app key/secret and temporary credentials from initial phase */
      $connection = new TwitterOAuth($this->_appId, $this->_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

      /* Request access tokens for this user from twitter */
      $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

      $this->log('Received access token from Twitter: '.print_r($access_token, true));

      /* Remove no longer needed request tokens */
      unset($_SESSION['oauth_token']);
      unset($_SESSION['oauth_token_secret']);

      /* Save the access tokens. Normally these would be saved in a database for future use. */
      $_SESSION['access_token'] = $access_token;
      $this->access_token = $access_token;
      /* If HTTP response is 200 continue otherwise send to connect page to retry */
      if (200 == $connection->http_code) {
        return new Zend_Auth_Result( Zend_Auth_Result::SUCCESS, $access_token['user_id'], array('user'=>array('id'=>$access_token['user_id'], 'screen_name'=>$access_token['screen_name']), 'token'=>$access_token) );
      } else {
        return new Zend_Auth_Result( Zend_Auth_Result::FAILURE, null, array('Error while handling callback.') );
      }

    }

    public function verifyCredentials(){
      require_once(BASE_PATH.'/library/twitteroauth/twitteroauth/twitteroauth.php');

      if(!isset($this->access_token)){
        return $this->redirectToTwitter();
      }

      //build a new connection using the access token for this user
      $connection = new TwitterOAuth($this->_appId, $this->_secret, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
      $user = $connection->get('account/verify_credentials', array('skip_status'=>true));

      if($connection->http_code === 200){
        $this->log('Verified Twitter credentials: '.print_r($user, true));
        return $user;
      }else{
        $this->log('Failed to verify twitter user credentials, received http status code: '.$connection->http_code.' with result object: '.print_r($user, true));
        return false;
      }

    }

    protected function log($msg){
      if(!isset($logger) && Zend_Registry::isRegistered('logger')){
        $this->logger = Zend_Registry::get('logger');
      }
      if($this->logger && $this->logger instanceof Zend_Log){
        $this->logger->info($msg);
      }
    }
}
?>
