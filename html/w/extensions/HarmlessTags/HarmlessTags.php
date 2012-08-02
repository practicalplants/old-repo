<?php

$wgHooks['ParserFirstCallInit'][] = 'harmlessTags';
 
// Hook our callback function into the parser
function harmlessTags( Parser $parser ) {
        // When the parser sees the <sample> tag, it executes 
        // the wfSampleRender function (see below)
        $parser->setHook( 'label', 'harmlessTagsRenderLabel' );
        // Always return true from this function. The return value does not denote
        // success or otherwise have meaning - it just must always be true.
        return true;
}
 
function harmlessTagsRenderLabel( $input, array $args, Parser $parser, PPFrame $frame ){
	return harmlessTagsRender('label',$input,$args);
}
function harmlessTagsRender( $tag, $input, array $args ) {
        // Nothing exciting here, just escape the user-provided
        // input and throw it back out again
		$attrs = array();
		foreach($args as $k => $v){
			$attrs[] = $k.'="'.$v.'"';
		}
		$attrs = implode(' ',$attrs);
        return '<'.$tag.' '.$attrs.'>'.$input.'</'.$tag.'>'; //htmlspecialchars( $input )
}