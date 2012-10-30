<?php
/**
 * MoveToSkin
 * A simple plugin which allows you to move content from a wiki article 
 * to predefined areas in your skin.
 * Intended for MediaWiki Skin designers.
 * By Andru Vallance - andru@tinymighty.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

$wgHooks['ParserFirstCallInit'][] = 'MoveToSkin::parserFirstCallInit';
$wgHooks['LanguageGetMagic'][] = 'MoveToSkin::languageGetMagic';
$wgHooks['ParserAfterTidy'][] = 'MoveToSkin::moveContent';


class MoveToSkin{
  
  public static $content = array();
  
  public static function parserFirstCallInit(&$parser){
  	$parser->setFunctionHook('movetoskin', 'MoveToSkin::parserFunction');
  	return true;
  }
  
  public static function languageGetMagic(&$magicWords){
  	$magicWords['movetoskin'] = array(0,'movetoskin');
  	return true;
  }
  
  public static function parserFunction($parser, $name='', $content=''){
    //we have to wrap the inner content within <p> tags, because MW screws up otherwise by placing a <p> tag before and after with related closing and opening tags within
    //php's DOM library doesn't like that and will swap the order of the first closing </p> and the closing </movetoskin> - stranding everything after that outside the <movetoskin> block. Lame.
    //$content = $parser->recursiveTagParse($content);
    $content = '<div class="movetoskin" data-target="'.$name.'">'.$content.'</div>';
    return array( $content, 'noparse' => false, 'isHTML' => false );
  }
  
  public static function moveContent(&$parser, &$html){
   if(empty($html))
     return true;
    //not sure why, but we have to UTF8 decode the html output... is it re-encoding it to non utf8?
    $html = utf8_decode($html);
    $doc = @DOMDocument::loadHTML( $html );
    //$doc->encoding = 'utf8';
    $xpath = new DomXPath($doc);
    $nodes = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' movetoskin ')]");
    //$movetoskins = $doc->getElementsByTagName('movetoskin');
    
    if($nodes->length > 0){
      foreach($nodes as $move){
        if($move->hasAttribute('data-target')){
          $target = $move->getAttribute('data-target');
          $inner_content = '';
          
          $move->removeAttribute('class');
          $move->removeAttribute('data-target');
          
          $inner_content = trim($doc->saveHTML( $move ));
          //chop off the starting <div> and ending </div> ... it's easier than trying to grab the contents via dom
          $inner_content = substr($inner_content, 5, count($inner_content)-7);
                    
          if(isset( self::$content[ $target ] )){
            array_push( self::$content[ $target ] , $inner_content );
          }else{
            self::$content[ $target ] = array(
              $inner_content 
            );
          }
          $parent = $move->parentNode;
          $parent->removeChild($move);
        }
      }
      
      $htmldoc = $doc->saveHTML( $doc->getElementsByTagName('body')->item(0) );
      if(preg_match('~<body>(.*?)<\/body>~si', $htmldoc, $match)){
        $html = $match[1];
      }
      $html = $html;
    }

    return true;
  }
    
  public static function hasContent($target){
    if(isset(self::$content[$target]))
      return true;
    return false;
  }
  
  public static function getContent($target=null){
    if($target!==null){
      if( self::hasContent($target) )
        return self::$content[$target];
      return array();
    }else{
      return self::$content;
    }
  }

}