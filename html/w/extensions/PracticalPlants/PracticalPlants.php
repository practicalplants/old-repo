<?php
include('SSO_Auth.php');
$wgAuth = new PracticalPlants_SSO_Auth();

$wgAutoloadClasses['PracticalPlants'] = dirname(__FILE__) . '/PracticalPlants_Body.php';
$wgAutoloadClasses['PracticalPlants_API'] = dirname(__FILE__) . '/api.php';
$wgAutoloadClasses['PracticalPlants_CommonsImages'] = dirname(__FILE__) . '/CommonsImages.php';


$ppResourceTemplate = array(
	'localBasePath' => dirname( __FILE__ ),
	'remoteExtPath' => 'PracticalPlants',
	'group' => 'ext.practicalplants'
);
$wgResourceModules['ext.practicalplants.css'] = $ppResourceTemplate + array(
        'styles' => array(
        	'modules/ext.practicalplants/css/main.css'=>array('media'=>'screen'),
        	'modules/ext.practicalplants/css/print.css'=>array('media'=>'print'),
        	'../../../resources/fonts/crete-round/stylesheet.css'=>array('media'=>'screen'),
        	'../../../resources/css/masthead.css'=>array('media'=>'screen')
        ),
        'position'=>'top'
 );
/*$wgResourceModules['ext.practicalplants.top'] = $ppResourceTemplate+=array(
        // JavaScript and CSS styles. To combine multiple files, just list them as an array.
        'scripts' => array( 'js/modernizr-1.7.min.js','js/jquery.ui.autocomplete-html.js','js/practicalplants.js' ),
        //'styles' => array('css/main.css'=>array('media'=>'screen')),
 
        // When your module is loaded, these messages will be available through mw.msg()
        //'messages' => array( 'myextension-hello-world', 'myextension-goodbye-world' ),
        
        'dependencies' => array( 'jquery.ui.autocomplete' )
 );*/
$wgResourceModules += array(
	'modernizr' => $ppResourceTemplate + array(
        'scripts' => array( 'modules/ext.practicalplants/js/modernizr-1.7.min.js' ),
        'dependencies' => array( 'jquery.ui.autocomplete' )
	),
	'augment' => $ppResourceTemplate + array(
	    'scripts' => array( 'modules/ext.practicalplants/js/augment.js' )
	),
	'jquery.ui.autocomplete.html' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/jquery.ui.autocomplete-html.js'),
		'dependencies' => array( 'jquery.ui.autocomplete' )
	),
	'ext.practicalplants.init.dom' => $ppResourceTemplate + array(
		'scripts' => array(
			'modules/ext.practicalplants/js/practicalplants.init.dom.js',
			'modules/ext.practicalplants/js/practicalplants.js'),
		'dependencies' => array( 'modernizr','augment' ),
		'position' => 'top'
	),
	'browserupdate' => $ppResourceTemplate + array(
		'scripts'=> array('modules/ext.practicalplants/js/browserupdate.js')
	),
	'ext.practicalplants.init' => $ppResourceTemplate + array(
		'scripts' => array(
			'modules/ext.practicalplants/js/practicalplants.init.mast-search.js',
			'modules/ext.practicalplants/js/practicalplants.init.forms.js',
			'modules/ext.practicalplants/js/practicalplants.init.article.js'),
		'dependencies' => array( 'jquery.ui.autocomplete.html', 'jquery.collapse','jquery.ui.tabs','jquery.qtip','ext.discover.js','jquery.ui.accordion','jquery.scrollto', 'mediawiki.api')
	),
	/*'jquery.cookie' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/jquery.cookie.js')
	),*/
	'ext.practicalplants.page.main'=> $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/practicalplants.page.main-discover.js'),
		'dependencies' => array( 'jquery.cookie')
	),
	'ext.practicalplants.page.search'=> $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/practicalplants.page.search-discover.js'),
		'dependencies' => array( 'jquery.cookie','ext.practicalplants.init')
	),
	'jquery.collapse' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/jquery.collapse.js'),
		'dependencies' => array( 'jquery.cookie','ext.practicalplants.init')
	),
	'jquery.scrollto' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/jquery.scrollto.min.js'),
		'dependencies' => array( 'jquery')
	),
	/*'jquery.colortip' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/colortip-1.0/colortip-1.0-jquery.js'),
		'styles' => array('modules/ext.practicalplants/js/colortip-1.0/colortip-1.0-jquery.css'),
		'dependencies' => array( 'jquery')
	),*/
	'jquery.qtip' => $ppResourceTemplate + array(
		'scripts' => array('modules/ext.practicalplants/js/jquery.qtip-1.0.0-rc3.min.js'),
		'dependencies' => array( 'jquery')
	)
);


$wgHooks['DoEditSectionLink'][] = 'PracticalPlants::doEditSectionLink';
$wgHooks['BeforePageDisplay'][] = 'PracticalPlants::loadResources';

$wgHooks['ParserAfterTidy'][] = 'PracticalPlants::parserAfterTidy';
$wgHooks['ParserFirstCallInit'][] = 'PracticalPlants::parserFirstCallInit';
$wgHooks['LanguageGetMagic'][] = 'PracticalPlants::languageGetMagic';
//$wgHooks['OutputPageParserOutput'][] = 'PracticalPlants::outputPageParserOutput';
$wgHooks['EditPage::showEditForm:initial'][] = 'PracticalPlants::onEditPage';

$wgHooks['sfEditFormPreloadText'][] = 'PracticalPlants::sfAddSpeciesChild';
//$wgHooks['sfSetTargetName'][] = 'PracticalPlants::setSpeciesChildName';

//$wgHooks['LinkBegin'][] = 'PracticalPlants::linkBegin';
$wgHooks['LinkEnd'][] = 'PracticalPlants::linkEnd';

/* Enable API */
$wgAPIModules['taxonomies'] = 'PracticalPlants_API';
