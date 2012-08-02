<?php

/**
 * Maintenance script allows creating or editing pages using
 * the contents of a text file
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
 * @file
 * @ingroup Maintenance
 * @author Rob Church <robchur@gmail.com>
 */

$options = array( );//'help', 'nooverwrite', 'norc' );
$optionsWithArgs = array( );//'title', 'user', 'comment' );
require_once( dirname( __FILE__ ) . '/commandLine.inc' );
$regex = '\|shelter for=(([A-Z a-z]+,?)+)';


function findPagesByRegex($regex){
	$dbr = wfGetDB( DB_SLAVE );
	$tables = array( 'page', 'revision', 'text' );
	$vars = array( 'page_id', 'page_namespace', 'page_title', 'old_text' );
	$comparisonCond = 'old_text REGEXP ' . $dbr->addQuotes( $regex );

	$conds = array(
		$comparisonCond,
		//'page_namespace' => $namespaces,
		'rev_id = page_latest',
		'rev_text_id = old_id'
	);
	
	$sort = array( 'ORDER BY' => 'page_namespace, page_title' );
	
	return $dbr->select( $tables, $vars, $conds, __METHOD__ , $sort );
}


$res = findPagesByRegex($regex);

$titles_for_edit = array();
$user = isset( $options['user'] ) ? $options['user'] : 'Maintenance script';

foreach ( $res as $row ) {
	$title = Title::newFromDBkey( $row->page_title );
	echo $title->getText()."\n";
	$article = new Article( $title, 0 );
	if ( !$article ) {
		echo 'replaceText: Article not found.'."\n";
		return false;
	}
	$content = $article->fetchContent();
	
	if(preg_match('%'.$regex.'%', $content, $m)){
		$functions = explode(',',$m[1]);
		echo 'Matched '.$m[1]."\n";
		$new_functions = '|shelter=';
		foreach($functions as $f){
			if($f==='Soil stabilization')
				$f='Earth stabiliser';
			if($f==='Soil reclamation')
				$f='Soil builder';
			$new_functions .= '{{Plant provides shelter for|function='.ucfirst(trim($f)).'}}';
		}
		
		echo 'Changing '.$m[0].' to '.$new_functions."\n";
		$new_content = preg_replace('%'.$regex.'%',$new_functions,$content);
		if($new_content!==$content){
			$edit_summary = 'Adjusting functions to use template-per-function rather than CSV list.';
			$article->doEdit( $new_content, $edit_summary, EDIT_MINOR );
			echo 'Done!'."\n";
		}else{
			echo 'No changes made'."\n";
		}
		
	}
	
}