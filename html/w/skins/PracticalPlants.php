<?php
/**
 * Custom skin for PracticalPlants.org
 *
 *
 * @todo document
 * @file
 * @ingroup Skins
 * @author Andru Vallance
 * @version 0.0.5
 * @license 
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );


/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
 
class SkinPracticalPlants extends SkinTemplate {
	var $skinname = 'practicalplants', $stylename = 'practicalplants',
		$template = 'PracticalPlantsTemplate', $useHeadElement = false;
	
	function initPage( OutputPage $out ) {
		parent::initPage( $out );
	}
	
	public function setContent($content){
	  $this->tpl->content = $content;
	}
	
	function setupTemplate( $classname, $repository = false, $cache_dir = false ) {
		$this->tpl = new PracticalPlantsTemplate();
		$this->tpl->content = MoveToSkin::getContent();
		return $this->tpl;
	}
	
	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;
		parent::setupSkinUserCss( $out );

		// Ugh. Can't do this properly because $wgHandheldStyle may be a URL
		//if( $wgHandheldStyle ) {
			// Currently in testing... try 'chick/main.css'
		//	$out->addStyle( $wgHandheldStyle, 'handheld' );
		//}

		/*$out->addStyle( 'practicalplants/IE50Fixes.css', 'screen', 'lt IE 5.5000' );
		$out->addStyle( 'practicalplants/IE55Fixes.css', 'screen', 'IE 5.5000' );
		$out->addStyle( 'practicalplants/IE60Fixes.css', 'screen', 'IE 6' );
		$out->addStyle( 'practicalplants/IE70Fixes.css', 'screen', 'IE 7' );*/

	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class PracticalPlantsTemplate extends BaseTemplate {

	/**
	 * @var Skin
	 */
	var $skin;
	
	

	/**
	 * Template filter callback for MonoBook skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		
		//uncomment for a quick data dump, since there's no documentation I can find that covers this...
		//echo '<pre>'; print_r($this->data); exit;
		
		global $wgLang;
		$this->skin = $this->data['skin'];

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();
		
		// Build additional attributes for navigation urls
		//$nav = $this->skin->buildNavigationUrls();
		$nav = $this->data['content_navigation'];

		if ( $wgVectorUseIconWatch ) {
			$mode = $this->skin->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
					' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
						' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
						Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
						Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];
    //print_r($this->data['namespace_urls']); exit;
		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

    require_once(realpath(__DIR__.'/../../../library').'/Masthead.php');
    $masthead = new PracticalPlants_Masthead(array(
    'active_tab'=>'wiki'/*,
    'search'=>'<form action="/w/index.php" id="searchform">
      <input type="hidden" name="title" value="Special:Search">
      <input name="search" title="Search Practical Plants [ctrl-option-f]" accesskey="f" id="searchInput" placeholder="Species/Taxonomy name or search term" class="search-field">      <input type="submit" name="go" value="Go" title="Go to a page with this exact name if exists" id="searchGoButton" class="searchButton">     <input type="submit" name="fulltext" value="Search" title="Search the pages for this text" id="mw-searchButton" class="searchButton">   </form>'*/
  ));
?>

<!DOCTYPE html>
<!--
<?php print_r($this->content);
print_r($_GET);
      	/*print_r($this->data);*/ ?>
 -->
<html lang="en" dir="ltr" class="client-nojs">
  <head>
    <title><?php echo $this->data['pagetitle'] ?></title>
    <meta charset="UTF-8" />
    <?php echo $this->data['headlinks']; ?>
    <?php echo $this->data['csslinks']; ?>
    <?php echo $this->data['pagecss']; ?>
    <?php echo $this->data['usercss']; ?>
    <?php echo $this->data['jsvarurl']; ?>
    <?php echo $this->data['headscripts']; ?>
  </head>
<body class="<?php echo $this->data['pageclass']; ?>">


<div id="page-wrapper">
	<?php 
	
	$masthead->output();
	?>
	
	<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
		
	<article id="main-entry" class="wiki-entry">
	  <?php if(isset($this->content['article state'])): ?>
	    <?php echo $this->content['article state'][0]; ?>
	  <?php endif; ?>
		<?php $header_class = ''; 
		  if(isset($this->content['article image']))
		    $header_class .= 'with-image';
		  if(isset($this->content['icon bar']))
		    $header_class .= ' with-iconbar';
		?>
		<header id="page-header" class="<?php echo $header_class?>">
		  <div class="width-constraint">
		    <div id="header-content">
    			<h1 id="article-title"><?php $this->html('title') ?><?php if(isset($this->content['common name'])) echo '<div id="common-name">'.$this->content['common name'][0].'</div>'; ?></h1>
    			<?php if(isset($this->content['article summary'])): ?><div id="article-summary"><?php echo '<p>'.implode('</p><p>',$this->content['article summary']).'</p>'; ?></div><?php endif; ?>
    			<div id="article-image-container"><?php if(isset($this->content['article image'])): ?><div id="article-image"><?php echo implode('',$this->content['article image']); ?></div><?php endif; ?></div>
    		</div>
  		</div>
  		<?php if(isset($this->content['header'])) echo implode('',$this->content['header']); ?>
  		<?php if(isset($this->content['icon bar'])) echo implode('',$this->content['icon bar']); ?>
  		<?php if(isset($this->content['use flags'])) echo implode('',$this->content['use flags']); ?>
  		<a id="beta-banner" href="/wiki/PracticalPlants:Beta"></a>
		</header>
		<div class="width-constraint">
  		<aside id="sidebar">
  		  <div id="page-buttons">
  	
  	  <?php
  	    /*foreach($this->data['view_urls'] as $type => $link):
  	      if(strpos($link['attributes'],'selected'))
  	        continue;
      	  switch($type):
      	    case 'view': ?>
      	    <a href="#" class="btn btn-large btn-success btn-block" id="sidebar-save-page-button"><i class="icon-ok icon-white"></i> Save Changes</a> 
      	    <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-arrow-left"></i> Return without Saving</a>  
            <?php break;
      	    case 'form_edit':  ?>
      	    <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-success btn-block"<?php echo $link['attributes'] ?>> <?php echo $link['key'] ?><i class="icon-pencil icon-white"></i> Edit This Page</a>
      	    <?php break;
      	    case 'edit': ?>
      	    <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-edit"></i> Edit Source</a>
      	    <?php break;
      	    case 'history': ?>
      	    <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-small"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-time"></i>  History</a>
      	    <?php break;
      	    endswitch;
      	  endforeach; */?>
      	
      	
      	<?php 

      	$action = isset($_GET['action']) ? $_GET['action'] : '';
      	echo substr( $this->data->thispage, 0, 16);
      	if($action==='' && isset($_GET['title'])){
      	  if( substr( $_GET['title'], 0, 16)  == 'Special:FormEdit')
      	    $action='formedit';
      	}
      	//View button (displayed on edit/history screens)
      	if( $action==='edit' || $action==='formedit' || $action==='submit'): ?>
      	  <a href="#" class="btn btn-large btn-success btn-block" id="sidebar-save-button"><i class="icon-ok icon-white"></i> Save Changes</a> 
      	<?php endif; ?>
      	<?php 
      	//View button (displayed on edit/history screens)
      	if(isset($this->data['view_urls']['view']) && !strpos($this->data['view_urls']['view']['attributes'],'selected') ): 
      	  $link = $this->data['view_urls']['view'];	?>
      	  <?php if($action==='submit'): ?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-danger btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-arrow-left icon-white"></i> Discard Changes &amp; Return</a>
      	  <?php else: ?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-arrow-left"></i> View Page</a>
      	  <?php endif; ?>  
      	<?php endif; ?>
      	<?php 
      	//form edit button
      	if(isset($this->data['view_urls']['form_edit']) && !strpos($this->data['view_urls']['form_edit']['attributes'],'selected') ): 
      	  $link = $this->data['view_urls']['form_edit'];	?>
      	  <?php if($action==='submit'): ?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-danger btn-block"<?php echo $link['attributes'] ?>> <?php echo $link['key'] ?><i class="icon-pencil icon-white"></i> Discard Changes &amp; Edit</a>
      	  <?php else: ?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large btn-success btn-block"<?php echo $link['attributes'] ?>> <?php echo $link['key'] ?><i class="icon-pencil icon-white"></i> Edit This Page</a>
      	  <?php endif; ?>  
      	  
      	<?php endif; ?>
      	<?php 
      	//edit source button
      	if(isset($this->data['view_urls']['edit']) && !strpos($this->data['view_urls']['edit']['attributes'],'selected') ): 
      	  $link = $this->data['view_urls']['edit'];	
      	  $is_primary = isset($this->data['view_urls']['form_edit']) ? false : true; //if this view has a form edit button, this button should be white, else green
      	  ?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-large <?php if($is_primary): ?>btn-success <?php endif; ?>btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-edit<?php if($is_primary): ?> icon-white<?php endif;?>"></i> Edit Source</a>
      	<?php endif; ?> 
        <?php 
        //edit source button
        if(isset($this->data['namespace_urls']) && !empty($this->data['namespace_urls']) && count($this->data['namespace_urls']) > 1):
          $content_ns = array_shift($this->data['namespace_urls']);
          $talk_ns = array_shift($this->data['namespace_urls']);
          if( strpos($content_ns['attributes'],'selected') ): ?>
          <a href="<?php echo htmlspecialchars( $talk_ns['href'] ) ?>" class="btn btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-book"></i> View Page Notes</a>
          <?php else: ?>
          <a href="<?php echo htmlspecialchars( $content_ns['href'] ) ?>" class="btn btn-block"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-arrow-left"></i> View Page Content</a>
          <?php 
          endif;
          ?>
        <?php endif; ?> 


      	<?php 
      	//history button
      	if(isset($this->data['view_urls']['history']) && !strpos($this->data['view_urls']['history']['attributes'],'selected') ): 
      	  $link = $this->data['view_urls']['history'];	?>
      	  <a href="<?php echo htmlspecialchars( $link['href'] ) ?>" class="btn btn-small"<?php echo $link['attributes'] ?> <?php echo $link['key'] ?>><i class="icon-time"></i>  History</a>
      	<?php endif; ?> 
  
  		  <?php if(!empty($this->data['action_urls'])): ?>
  		    <div class="dropdown" id="article-actions-dropdown">
  		      <a id="article-actions-menu" class="btn btn-small dropdown-toggle" data-toggle="dropdown"><i class="icon-file"></i> <?php $this->msg( 'actions' ) ?> <b class="caret"></b></a>
  	    		<ul<?php $this->html( 'userlangattributes' ) ?> class="dropdown-menu pull-right">
  	    			<?php foreach ( $this->data['action_urls'] as $link ): ?>
  	    				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
  	    			<?php endforeach; ?>
  	    		</ul>
  		    </div>
  		  <?php endif; ?>


  		  <div class="dropdown" id="article-toolbox-dropdown" <?php echo Linker::tooltip( 'p-tb' ) ?>>
  		  	<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" <?php $this->html( 'userlangattributes' ) ?>>
  		  	  <i class="icon-wrench"></i> <b class="caret"></b> <?php /*$msgObj = wfMessage( 'toolbox' ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : 'toolbox' );*/ ?></a>
  		  		<ul class="dropdown-menu pull-right">
  		  <?php
  		  			foreach( $this->getToolbox() as $key => $val ): ?>
  		  			<?php echo $this->makeListItem( $key, $val ); ?>
  		  
  		  <?php
  		  			endforeach;
  		  			if ( isset( $hook ) ) {
  		  				wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this, true ) );
  		  			}
  		  			?>
  		  		</ul>
  		  </div>
  		    
  		    
  		  </div>
  		  <div id="toc-container">
  		    <?php if(isset($this->content['toc'])) echo $this->content['toc'][0] ?>
  		  </div>

  		</aside>
  		
  		<div id="after-header"></div>
  		<div class="article-content">
  		<?php if($this->data['undelete']) { ?>
  				<div id="contentSub2"><?php $this->html('undelete') ?></div>
  		<?php } ?><?php if($this->data['newtalk'] ) { ?>
  				<div class="usermessage"><?php $this->html('newtalk')  ?></div>
  		<?php } ?><?php /*if($this->data['showjumplinks']) { ?>
  				<div id="jump-to-nav"><?php $this->msg('jumpto') ?> <a href="#column-one"><?php $this->msg('jumptonavigation') ?></a>, <a href="#searchInput"><?php $this->msg('jumptosearch') ?></a></div>
  		<?php } */ ?>
  		<a id="top"></a>	
  		<!-- start content -->	
  		<?php $this->html('bodytext') ?>
  		<?php if($this->data['catlinks']) { $this->html('catlinks'); } ?>
  		<!-- end content -->
  		<?php if($this->data['dataAfterContent']) { $this->html ('dataAfterContent'); } ?>
  		<div class="visualClear"></div>
  		</div>
		</div><!--/width-constraint-->
		
	</article>

  

	<nav id="menubar"<?php $this->html('userlangattributes')  ?> class="masthead-submenu navbar">
	  <ul class="nav">
	    <li><a href="/wiki/"><i class="icon-home icon-white"></i> Home</a></li>
	    <li><a href="/wiki/Search"><i class="icon-search icon-white"></i> Advanced Search</a></li>
	    <li class="dropdown">
    	    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user icon-white"></i> Your Pages <b class="caret"></b></a>
      		<ul class="dropdown-menu"<?php $this->html( 'userlangattributes' ) ?>>
      		<?php 
      $usertools = $this->getPersonalTools();
      //print_r($usertools);exit;
      //remove signup and login links
      if(isset($usertools['anonlogin']))
      	unset($usertools['anonlogin']);
      if(isset($usertools['logout']))
      	unset($usertools['logout']);
      	
      				foreach($usertools  as $key => $item ) { ?>
      			<?php echo $this->makeListItem( $key, $item ); ?>
      	
      	<?php			} ?>
      		</ul>
    	</li>
      <li><a href="/wiki/Help:Contents"><i class="icon-question-sign icon-white"></i> Help</a></li>
    </ul>
    <ul class="nav pull-right">
    	<li id="search-nav">
    	  <i class="icon-search icon-white"></i>
    	  <form action="/w/index.php" id="searchform">
    	  	<input type="hidden" name="title" value="Special:Search">
    	  	<input name="search" title="Search All Plants" accesskey="f" id="plant-search" placeholder="Enter a plant name or search term..." class="search-field">
    	  	<input type="submit" name="go" value="Go" title="Go to a page with this exact name if exists" id="searchGoButton" class="searchButton">			<input type="submit" name="fulltext" value="Search" title="Search the pages for this text" id="mw-searchButton" class="searchButton">
    	  </form>
    	</li>
    </ul>
		
	</nav><!-- end of the left (by default at least) column -->
	
	</div><!--/width-constraint-->
	<div class="clear"></div>
	
  <?php 
  $links = $this->getFooterLinks(); 
  $info = '<ul>';
  
  foreach( $links['info'] as $link ){
    $info .= '<li>' . $this->data[$link] .'</li>';
  }
  $info .= '</ul>';

  $masthead->footer(
    array(
      array(
        'id'=>'mw-footer-info',
        'title'=>"Page Info",
        'content'=>$info
      )
    )
  ); ?>

</div>

<?php
		$this->printTrail();
		include(realpath(__DIR__.'/../../../library').'/google-analytics.html');
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
		wfRestoreWarnings();
	} // end of execute() method

	/*************************************************************************************************/

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	private function renderPortals( $portals=array() ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;

			echo "\n<!-- {$name} -->\n";
			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}

	private function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( !isset( $msg ) ) {
			$msg = $name;
		}
		?>
<div class="" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
	<h5<?php $this->html( 'userlangattributes' ) ?>><?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h5>
	<div class="dropdown-content">
<?php
		if ( is_array( $content ) ): ?>
		<ul>
<?php
			foreach( $content as $key => $val ): ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			endforeach;
			if ( isset( $hook ) ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		else: ?>
		<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
<?php
		endif; ?>
	</div>
</div>
<?php
	}
	
	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
			global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;
	
			// If only one element was given, wrap it in an array, allowing more
			// flexible arguments
			if ( !is_array( $elements ) ) {
				$elements = array( $elements );
			// If there's a series of elements, reverse them when in RTL mode
			} elseif ( $wgLang->isRTL() ) {
				$elements = array_reverse( $elements );
			}
			// Render elements
			foreach ( $elements as $name => $element ) {
				echo "\n<!-- {$name} -->\n";
				switch ( $element ) {
					case 'NAMESPACES':
	?>
	<div id="article-namespaces" class="tabs<?php if ( count( $this->data['namespace_urls'] ) == 0 ) echo ' empty'; ?>">
		<h5><?php $this->msg( 'namespaces' ) ?></h5>
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
				<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
					break;
					case 'VARIANTS':
	?>
	<div id="article-variants" class="dropdown<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' empty'; ?>">
		<?php if ( $wgVectorShowVariantName ): ?>
			<h4>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
					<?php echo htmlspecialchars( $link['text'] ) ?>
				<?php endif; ?>
			<?php endforeach; ?>
			</h4>
		<?php endif; ?>
		<h5><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h5>
		<div class="menu">
			<ul<?php $this->html( 'userlangattributes' ) ?>>
				<?php foreach ( $this->data['variant_urls'] as $link ): ?>
					<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
					break;
					case 'VIEWS':
	?>
	<div id="article-views" class="tabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' empty'; } ?>">
		<h5><?php $this->msg('views') ?></h5>
		<ul<?php $this->html('userlangattributes') ?>>
			<?php foreach ( $this->data['view_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
					// $link['text'] can be undefined - bug 27764
					if ( array_key_exists( 'text', $link ) ) {
						echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
					}
					?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
					break;
					case 'ACTIONS':
	?>
	<div id="article-actions" class="dropdown<?php if ( count( $this->data['action_urls'] ) == 0 ) echo ' empty'; ?>">
		<h5><?php $this->msg( 'actions' ) ?></h5>
		<div class="menu">
			<ul<?php $this->html( 'userlangattributes' ) ?>>
				<?php foreach ( $this->data['action_urls'] as $link ): ?>
					<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php
					break;
					case 'PERSONAL':
	?>
	<div id="account" class="dropdown<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' empty'; ?>">
		<h5><?php $this->msg( 'personaltools' ) ?></h5>
		<ul class="dropdown-content" <?php $this->html( 'userlangattributes' ) ?>>
		<?php 
$usertools = $this->getPersonalTools();
//print_r($usertools);exit;
//remove signup and login links
if(isset($usertools['anonlogin']))
	unset($usertools['anonlogin']);
if(isset($usertools['logout']))
	unset($usertools['logout']);
	
				foreach($usertools  as $key => $item ) { ?>
			<?php echo $this->makeListItem( $key, $item ); ?>
	
	<?php			} ?>
		</ul>
	</div>
	<?php
					break;
					case 'SEARCH':
	?>
	<div id="search">
		<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
		<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
			<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type'=>'text','placeholder' => 'Species/Taxonomy name or search term', 'class'=>'search-field autocompleteInput',  'autocompletesettings'=>'All taxonomies') ); ?>
			<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
			<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		</form>
	</div>
	<?php
	
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
	
} // end of class


