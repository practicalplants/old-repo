<?php
# HForCommentsButNotInTOC.php
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/YourExtensionName.php");

$wgExtensionFunctions[] = "wfForComments";
 
function wfForComments() {
    global $wgParser;
    # register the extension with the WikiText parser
    # the first parameter is the name of the new tag.
    # the second parameter is the callback function for
    # processing the text between the tags
    $wgParser->setHook( "xh1", "renderxh1" );
    $wgParser->setHook( "xh2", "renderxh2" );
    $wgParser->setHook( "xh3", "renderxh3" );
    $wgParser->setHook( "xh4", "renderxh4" );
    $wgParser->setHook( "xh5", "renderxh5" );
    $wgParser->setHook( "xh6", "renderxh6" );
}
 
# Helper function to output headings
function renderxh($input, $level, $parser)
{
    return "<h$level>" . $parser->recursiveTagParse($input) . "</h$level>";
}
 
# The callback functions for converting the input text to HTML output
function renderxh1( $input, $argv, $parser ) {
    return renderxh($input, 1, $parser);
}
function renderxh2( $input, $argv, $parser ) {
    return renderxh($input, 2, $parser);
}
function renderxh3( $input, $argv, $parser ) {
    return renderxh($input, 3, $parser);
}
function renderxh4( $input, $argv, $parser ) {
    return renderxh($input, 4, $parser);
}
function renderxh5( $input, $argv, $parser ) {
    return renderxh($input, 5, $parser);
}
function renderxh6( $input, $argv, $parser ) {
    return renderxh($input, 6, $parser);
}
?>