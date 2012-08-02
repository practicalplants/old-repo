<?php

/**
 * Initialization file for the Semantic Image Input extension.
 *
 * @file SemanticImageInput.php
 * @ingroup SII
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

/**
 * This documentation group collects source code files belonging to Semantic Image Input.
 *
 * @defgroup SII Semantic Image Input
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/*if ( version_compare( $wgVersion, '1.17c', '<' ) ) { // Needs to be 1.17c because version_compare() works in confusing ways
	die( '<b>Error:</b> Semantic Image Input requires MediaWiki 1.17 or above.' );
}
*/
// Show an error if Semantic MediaWiki is not loaded.
if ( ! defined( 'SMW_VERSION' ) ) {
	die( '<b>Error:</b> You need to have <a href="http://semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> installed in order to use Semantic Ajax Create input.<br />' );
}

// Show an error if Semantic MediaWiki is not loaded.
if ( ! defined( 'SF_VERSION' ) ) {
	die( '<b>Error:</b> You need to have <a href="https://www.mediawiki.org/wiki/Extension:Semantic_Forms">Semantic Forms</a> installed in order to use Semantic Ajax Create input.<br />' );
}

define( 'SACI_VERSION', '0.1b' );

$wgExtensionCredits['semantic'][] = array(
	'path' => __FILE__,
	'name' => 'Semantic  Ajax Create Input',
	'version' => SACI_VERSION,
	'author' => array( 'Andru Vallance'	),
	'url' => 'https://www.mediawiki.org/wiki/Extension:Semantic_Ajax_Create_input',
	'descriptionmsg' => 'sii-desc'
);

// i18n
$wgExtensionMessagesFiles['saci']				= dirname( __FILE__ ) . '/SemanticAjaxCreateInput.i18n.php';

// Autoloading
$wgAutoloadClasses['SACISettings'] 				= dirname( __FILE__ ) . '/SemanticAjaxCreateInput.settings.php';

$wgAutoloadClasses['AjaxCreateInput'] 		= dirname( __FILE__ ) . '/includes/AjaxCreateInput.php';

$wgExtensionFunctions[] = function() {
	global $sfgFormPrinter;
	$sfgFormPrinter->registerInputType( 'AjaxCreateInput' );
};

// Resource loader modules
$moduleTemplate = array(
	'localBasePath' => dirname( __FILE__ ) . '/resources',
	'remoteExtPath' => 'SemanticAjaxCreateInput/resources'
);

$wgResourceModules['ext.saci.main'] = $moduleTemplate + array(
	'scripts' => array(
		'semantic-ajax-create.js'
	),
);

unset( $moduleTemplate );