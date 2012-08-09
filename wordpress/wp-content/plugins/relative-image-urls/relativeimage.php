<?php
/**
 * @package Relative Image URLs
 * @author BlueLayerMedia
 * @version 1.0.0
 */
/*
Plugin Name: Relative Image URLs
Plugin URI: http://www.bluelayermedia.com/
Description: Replaces absolute URLs with Relative URLs for image paths in posts
Author: BlueLayerMedia
Version: 1.0.0
Author URI: http://www.bluelayermedia.com/
*/

add_filter('image_send_to_editor','image_to_relative',5,8);

function image_to_relative($html, $id, $caption, $title, $align, $url, $size, $alt)
{
	$sp = strpos($html,"src=") + 5;
	$ep = strpos($html,"\"",$sp);
	
	$imageurl = substr($html,$sp,$ep-$sp);
	
	$relativeurl = str_replace("http://","",$imageurl);
	$sp = strpos($relativeurl,"/");
	$relativeurl = substr($relativeurl,$sp);
	
	$html = str_replace($imageurl,$relativeurl,$html);
	
	return $html;
}

?>