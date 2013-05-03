<?php
/**
 * This script imports images from WikiSpecies
 *
 * Usage:
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
 * @ingroup Maintenance
 */

require_once( dirname(dirname( __FILE__ )) . '/Maintenance.php' );

class MigrateLicense extends Maintenance {
  public function __construct() {
    parent::__construct();
    $this->mDescription = "Move over all pfaf text in plant templates.";
    $this->addOption( 'from_title', 'What item to start from', false, true );
    $this->addOption( 'title', 'A single title to grab', false, true );
    $this->addOption( 'unmodified_since', 'Select article which have not been modified since at least this date.', false, true);
    //$this->addOption( 'type', 'Type of job to run', false, true );
    //$this->addOption( 'procs', 'Number of processes to use', false, true );
  }

  public function memoryLimit() {
    // Don't eat all memory on the machine if we get a bad job.
    return "150M";
  }

  public function execute() {
    global $wgTitle;
    $res = $this->queryPages();
    if(!$res || count($res) === 0){
      $this->log('No results');
    }else{
      $this->log(count($res).' results found');
    }
    if ( $this->hasOption( 'from_title' ) ) {
      $from_title = trim( $this->getOption( 'from_title' ) );
      $skip = true;
    }
    if( $this->hasOption('unmodified_since') ){
      $before_date = strtotime($this->getOption('unmodified_since') );
    }
        
    //echo $from; exit;
    //$this->log(print_r($res,true));
    foreach ( $res as $row ) {
      if(isset($from_title)){
        if(trim($row->page_title) == $from_title)
          $skip = false;
        if($skip){
          $this->log( 'Skipping '.$row->page_title);
          continue;
        }
      }
      $title = Title::newFromText( $row->page_title );
      $article = new Article( $title, 0 );
      
      if ( !$article ) {
        $this->log( 'Page title not found: '.$row->page_title );
        continue;
      }
      $page = $article->getPage();

      //it would probably be better to do this via a join on the most recent revision's timestamp field, but whatever...
      
      if(isset($before_date)){
        $time = strtotime( $page->getTimestamp() );
        if($time > $before_date){
          $this->log('Skipping '.$row->page_title.' - has been modified since specified date: '.date('Y-M-d',$before_date).' '.$this->getOption('unmodified_since'));
          continue;
        }
      }
      $content = $article->fetchContent();
      
      if(preg_match('%{{Plant%ui',$content, $m)){
        $has_tpl = preg_match('%{{Plant%ui', $content, $matched);
        if($has_tpl){
          $this->log($title->getText());
          $this->log('Migrating article to CC-BY-SA');
          
          /*$content = preg_replace('%|cultivation=%i', "|cultivation notes=|PFAF cultivation notes=" ,$content, 1);
          $content = preg_replace('%|propagation=%i', "|propagation notes=|PFAF propagation notes=", $content, 1);
          $content = preg_replace('%|toxicity notes=%i', "|toxicity notes=|PFAF toxicity notes=", $content, 1);
          $content = preg_replace('%|edible use notes=%i', "|edible use notes=|PFAF edible use notes=", $content, 1);
          $content = preg_replace('%|material use notes=%i', "|material use notes=|PFAF material use notes=", $content, 1);
          $content = preg_replace('%|medicinal use notes=%i', "|medicinal use notes=|PFAF medicinal use notes=", $content, 1);*/

          //since we're replacing with content which contains the replace match, we need to use an intermediate step
          /*$content = preg_replace('%|(toxicity|edible use|material use|medicinal use) notes=%', '|$1 replace=', $content);

          $patterns = array(
            '%|(cultivation)=%', 
            '%|(propagation)=%', 
            '%|((toxicity|edible use|material use|medicinal use) replace)=%', 
          );
          $replacements = array(
            "|cultivation notes=|PFAF cultivation notes=", 
            "|propagation notes=|PFAF propagation notes=", 
            "|$1=|PFAF $1=", 
          );
          $content = preg_replace($patterns, $replacements, $content);

          */
         
         $renames = array(
            '|cultivation=' => "|cultivation notes=\n|PFAF cultivation notes=",
            '|propagation=' => "|propagation notes=\n|PFAF propagation notes=",
            '|toxicity notes=' => "|toxicity notes=\n|PFAF toxicity notes=",
            '|edible use notes=' => "|edible use notes=\n|PFAF edible use notes=",
            '|material use notes=' => "|material use notes=\n|PFAF material use notes=",
            '|medicinal use notes=' => "|medicinal use notes=\n|PFAF medicinal use notes="
          );
          
          foreach($renames as $old => $new){
            $check = stripos($content, $new);
            if($check){
              $this->log('Article appears to have already been migrated. Skipping.');
              break;
            }
            $pos = stripos($content, $old);
            if($pos){
              /*$this->log('Replacing text found at '.$pos.' - '. ($pos+strlen($old)) );
              $this->log(substr($content, $pos, strlen($old) ) );
              $this->log('with');
              $this->log( $new );*/
              $content = substr_replace($content, $new, $pos, strlen($old));
            }
          }
          

          $edit_summary = 'Migrating article to Creative Commons BY-SA, isolating PFAF NC content for manual migration. See the page: Migrating PFAF Licensing';
          
          $user = User::newFromName('Bot');

          $article->doEdit( $content, $edit_summary, EDIT_FORCE_BOT+EDIT_SUPPRESS_RC, $user );
                      
        }else{
          $this->log($row->page_title.' does not have the Plant template. Skipping.');
        }
        
      }
    }   
    
  }
  
  public function queryPages(){
    $dbr = wfGetDB( DB_SLAVE );
    $tables = array( 'page','categorylinks' );
    $vars = array( 'page_id', 'page_namespace', 'page_title' );
    
    $category = Title::newFromText( 'Category:Plant' )->getDbKey();
        
    $conds = array(
      //'page_namespace' => $namespaces,
      'page_id = cl_from',
      'cl_to' => $category
    );
    
    if ( $this->hasOption( 'title' ) ) {
      $title = trim( $this->getOption( 'title' ) );
      $conds['page_title'] = $title;
    }
    
    $sort = array( 'ORDER BY' => 'page_title' );

    return $dbr->select( $tables, $vars, $conds, __METHOD__ , $sort );
  }

  /**
   * Log the job message
   * @param $msg String The message to log
   */
  private function log( $msg ) {
    $this->output( " $msg\n" );
    wfDebugLog( 'AddPrimaryImages', $msg );
  }
}

$maintClass = "MigrateLicense";
require_once( RUN_MAINTENANCE_IF_MAIN );
