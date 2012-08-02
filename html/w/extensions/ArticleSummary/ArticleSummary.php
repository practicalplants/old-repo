<?php

// Extension credits that will show up on Special:Version    
$wgExtensionCredits['validextensionclass'][] = array(
        'path'           => __FILE__,
        'name'           => 'ArticleSummary',
        'version'        => '1.0',
        'author'         => 'Andru Vallance, TinyMighty.com', 
        'url'            => 'http://www.tinymighty.com',
        'descriptionmsg' => 'articlesummary',
        'description'    => 'Provides the <summary> tag to create an article summary.'
);


//exit 'test';

//$wgHooks['BeforePageDisplay'][] = 'ArticleSummary::beforePageDisplayHook';
$wgHooks['ParserFirstCallInit'][] = 'ArticleSummary::parserHook';
$wgHooks['LanguageGetMagic'][] = 'ArticleSummary::registerMagicWords';

class ArticleSummary{
	
	public static $summary = '';
	
	function parserHook( Parser $parser ) {
        $parser->setHook( 'summary', 'ArticleSummary::summaryTag' );
		$parser->setFunctionHook( 'summary', 'ArticleSummary::summaryFunction' );
        return true;
	}
	
	function summaryTag($input, array $args, $parser, PPFrame $frame){
		self::$summary = $parser->recursiveTagParse( $input, $frame );
		return $input;
	}
	
	function summaryFunction($input, array $args, $parser, PPFrame $frame){
		self::$summary = $input;
		return $input;
	}
	

	function registerMagicWords(&$magicWords, $langCode) {
	   $magicWords['summary'] = array(0, 'summary');
	   return true;
	}
}
/*
function ArticleSummaryParserHook( Parser $parser ) {
	exit;
    $parser->setHook( 'lol', 'articleSummarySummaryTag' );
    return true;
}
function articleSummarySummaryTag($input, array $args, $parser, PPFrame $frame){
	//ArticleSummary::$summary = $parser->recursiveTagParse( $input, $frame );
	return 'lol';
}
*/