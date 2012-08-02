<?php

if ( ! defined( 'MEDIAWIKI' ) )
	die();

# @package MediaWiki
# @subpackage Extensions
#
# MediaWiki Strip Markup extension 
# To install, copy the extension to your extensions directory and add line
# include("extensions/StripMarkup.php");
# to the bottom of your LocalSettings.php
#
# @author Steve Sanbeg
# @copyright Copyright Â© 2006, Steve Sanbeg
# @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later


$wgExtensionFunctions[]="wfStripMarkupExtension";
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'StripMarkup',
	'author' => 'Steve Sanbeg',
	'description' => 'adds <nowiki><stripmarkup></nowiki> tag',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Strip_Markup'
);

function wfStripMarkupExtension() {
	$GLOBALS['wgParser']->setHook("stripmarkup","StripMarkupExtension");
}

function StripMarkupExtension( $text, $param=array(), $parser=null ) {
	$text = preg_replace('~\[\[(.+)(|.+)?\]\]~i','$1',$text);
	$text = $parser->recursiveTagParse($text);
    // echo $text;
    //echo $text; exit;
    //echo  Sanitizer::stripAllTags( $text ); //exit;
    return trim(Sanitizer::stripAllTags( $text ));
}

?>