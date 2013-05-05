<?php
/**
 * Webjawns Login Form
 *
 * @package Webjawns
 * @subpackage Auth
 */
class Application_Form_Login extends Zend_Form {
 
    public function init() {
        $username = $this->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array('EmailAddress', array('StringLength', false, array(3, 255))),
            'required' => true,
            'label' => 'Email',
        ));
 
        $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array( array('StringLength', false, array(5, 20))),
            'required' => true,
            'label' => 'Password',
        ));
        
        $login = $this->addElement('checkbox', 'persist', array(
            'label' => 'Remember me until I logout.',
        ));
 
        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Login',
        ));
        
        $redirect = $this->addElement('hidden', 'redirect');
 
        // Displays 'authentication failed' message if absolutely necessary
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
            array('Description', array('placement' => 'prepend')),
            'Form'
        ));

        $this->addDecorator( 'Errors', array( 'placement' => Zend_Form_Decorator_Abstract::PREPEND ) );
    }
 
}