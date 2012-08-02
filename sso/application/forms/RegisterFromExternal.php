<?php
/**
 * Webjawns Login Form
 *
 * @package Webjawns
 * @subpackage Auth
 */
class Application_Form_RegisterFromExternal extends Zend_Form {
 
    public function init() {
    	
    	$email = $this->addElement('text', 'email', array(
    	    'filters' => array('StringTrim', 'StringToLower'),
    	    'validators' => array('EmailAddress'),
    	    'required' => true,
    	    'label' => 'Email Address',
    	));
    	
        $username = $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array('Alnum', array('StringLength', false, array(3, 20))),
            'required' => true,
            'label' => 'Username',
        ));
        
        $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array( array('StringLength', false, array(5, 20))),
            'required' => true,
            'label' => 'Password',
        ));
 		
 		$login = $this->addElement('text', 'display_name', array(
 		    'required' => false,
 		    'ignore' => false,
 		    'label' => 'Real name',
 		));
 		
        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Register',
        ));
        
        // Displays 'authentication failed' message if absolutely necessary
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));
    }
 
}