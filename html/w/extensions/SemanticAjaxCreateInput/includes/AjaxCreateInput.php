<?php

/**
 * Form input for adding images from Wikipedia or Wikimedia Commons.
 * 
 * @since 0.1
 * 
 * @file InstantImageInput.php
 * @ingroup SFFormInput
 * @ingroup SII
 * 
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class AjaxCreateInput extends SFFormInput {
	
	public static function getName() {
		return 'ajaxcreate';
	}

	public static function getDefaultPropTypes() {
		return array();
	}

	public static function getOtherPropTypesHandled() {
		return array( '_txt', '_wpg' );
	}

	public static function getDefaultPropTypeLists() {
		return array();
	}
	
	public static function getOtherPropTypeListsHandled() {
		return array();
	}
	
	/**
	 * Returns the HTML code to be included in the output page for this input.
	 */
	public function getHtmlText() {
		return self::getHTML(
			$this->mCurrentValue,
			$this->mInputName,
			$this->mIsMandatory,
			$this->mIsDisabled,
			$this->mOtherArgs
		);
	}
	
	public static function getParameters() {
		$params = parent::getParameters();
		
		$params[] = array(
			'name' => 'type',
			'type' => 'str',
			'description' => wfMsg( 'sii-imageinput-type' )
		); // page
		
		$params[] = array(
			'name' => 'hide',
			'type' => 'bool',
			'description' => wfMsg( 'sii-imageinput-hide' )
		); // false
		
		$params[] = array(
			'name' => 'width',
			'type' => 'int',
			'description' => wfMsg( 'sii-imageinput-width' )
		); // 200
		
		$params[] = array(
			'name' => 'showdefault',
			'type' => 'bool',
			'description' => wfMsg( 'sii-imageinput-showdefault' )
		); // true
		
		$params[] = array(
			'name' => 'list query',
			'type' => 'str',
			'description' => wfMsg( 'sii-imageinput-listquery' )
		); // true
		
		return $params;
	}
	
	public static function getHTML( $cur_value, $input_name, $is_mandatory, $is_disabled, $other_args ) {
		global $wgOut;
		$html = 'Ajax Create Input';
		
		
		$el_id = 'saci-'.substr(uniqid(),0,3);
		$attrs = array('id'=>$el_id);
		
			$wgOut->addModules( 'ext.saci.main' );
			$wgOut->addScript('<script>mw.loader.using( \'ext.saci.main\', function () {
				SemanticAjaxCreate.init({ container:\'#'.$el_id.'\', pagename:\''.self::getPage().'\' });
			} );</script>');
						
			$html = Html::rawElement(
				'div',
				$attrs,
				$html
			);
		
		return $html;
	}
	
	protected static function getPage() {
		$parts = explode( '/', $GLOBALS['wgTitle']->getFullText() );
		
		// TODO: this will not work for non-en.
		if ( $parts[0] == 'Special:FormEdit' ) {
			array_shift( $parts );
			array_shift( $parts );
		}
		
		return implode( '/', $parts );
	}
	
}
