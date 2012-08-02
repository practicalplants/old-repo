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

$evergreen = array();

$titles_for_edit = array();
$user = isset( $options['user'] ) ? $options['user'] : 'Maintenance script';

foreach ( $evergreen as $title ) {
	$title = Title::newFromDBkey( $title );
	echo $title->getText()."\n";
	$article = new Article( $title, 0 );
	if ( !$article ) {
		echo 'replaceText: Article not found.'."\n";
		return false;
	}
	$content = $article->fetchContent();
	
	if(preg_match('%\|deciduous or evergreen=(([\w\s]+,?)+)\|%', $content, $m)){
		echo 'Matched '.$m[1]."\n";
		$update = "|deciduous or evergreen=evergreen\n|";
		echo 'Changing '.$m[0].' to '.$update."\n";
		$new_content = preg_replace('%\|deciduous or evergreen=(([\w\s]+,?)+)%',$update,$content);
		if($new_content!==$content){
			$edit_summary = 'Correction for import error. Changing this plant to evergreen.';
			$article->doEdit( $new_content, $edit_summary, EDIT_MINOR );
			echo 'Done!'."\n";
		}else{
			echo 'No changes made'."\n";
		}
		
	}
}