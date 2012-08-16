<?php
/*
Plugin Name: Practical Plants SSO Authentication
Version: 0.1
Plugin URI: 
Description: Authenticate users using Practical Plants SSO - based on http://github.iamcal.com
Author: Andru Vallance
Author URI: http://tinymighty.com
*/

class PracticalPlantsAuthAuthenticationPlugin {

	
	function __construct() {
		add_filter('login_url', array(&$this, 'bypass_reauth'));
		add_filter('show_password_fields', array(&$this, 'disable'));
		add_filter('allow_password_reset', array(&$this, 'disable'));
		add_action('check_passwords', array(&$this, 'generate_password'), 10, 3);
		add_action('wp_logout', array(&$this, 'logout'));
	}
	
	function sso_user(){	
		$user = $this->shareSession();
		if( $user && isset($user->username) ){
			return $user->username;
		}
		return false;
	}
	function sso_email(){ 
		$user = $this->shareSession();
		if( $user && isset($user->email) ){
			return $user->email;
		}
		return false;
	}
	function sso_logout(){	
		return SSO_URL.'/logout'; 
	}
	
	
	function shareSession(){
		$sso_user = false;
		if(isset($_SESSION['sso_user'])){
			$sso_user = $_SESSION['sso_user'];
		}else{
			$cookies = array();
			foreach($_COOKIE as $k=>$v){
				$cookies[] = $k.'='.$v;
			}
			$ch = curl_init(SSO_URL.'/integration/wordpress/share-session');
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
		return $sso_user;
	}
	


	#
	# filters & actions
	#

	function bypass_reauth($login_url){
		return remove_query_arg('reauth', $login_url);
	}

	function disable($flag){

		return false;
	}

	function generate_password($username, $password1, $password2){

		$password1 = $password2 = wp_generate_password();
	}

	function logout(){

		header('Location: '.$this->sso_logout());
		exit;
	}


	#
	# the guts
	#

	function check_remote_user(){

		$username = $this->sso_user();

		if (!$username){
			return new WP_Error('empty_username', 'No remote user found.'.print_r($username,true));
		}

		$user = get_userdatabylogin($username);
		if (!$user){
			$password = wp_generate_password();
			$email = $this->sso_email();

			require_once(WPINC . DIRECTORY_SEPARATOR . 'registration.php');

			$user_id = wp_create_user($username, $password, $email);
			$user = get_user_by('id', $user_id);
		}

		return $user;
	}
}

$sso_authentication_plugin = new PracticalPlantsAuthAuthenticationPlugin();

// Override pluggable function to avoid ordering problem with 'authenticate' filter
if (!function_exists('wp_authenticate')){
	function wp_authenticate($username, $password){
		global $sso_authentication_plugin;

		$user = $sso_authentication_plugin->check_remote_user();
		if (!is_wp_error($user)){
			$user = new WP_User($user->ID);
		}

		return $user;
	}
}

?>
