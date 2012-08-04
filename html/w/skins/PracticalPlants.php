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
		$template = 'PracticalPlantsTemplate', $useHeadElement = true;
	
	function initPage( OutputPage $out ) {
		parent::initPage( $out );
		
		//echo '<pre>'; print_r($out); exit;
		
		//$out->addModules( 'skins.practicalplants' );
		
		//format title...
		//if( is_array($out->mCategories) && in_array('Plant',$out->mCategories) ){
		//	$title = $out->getPageTitle();
		//	$title = PracticalPlants::formatSpeciesName($title);
		//	$out->setPageTitle($title);
		//}
		
		/*if(preg_match('$<div id="article-summary">(.+)</div>$i', $out->mBodytext, $matches)){
			$out->mBodytext = preg_replace('$<div id="article-summary">(.+)</div>$i', '', $out->mBodytext);
			$this->article_summary = $matches[0];
		}
		if(preg_match('$<div id="article-image">(.+)</div>$i', $out->mBodytext, $matches)){
			$out->mBodytext = preg_replace('$<div id="article-image">(.+)</div>$i', '', $out->mBodytext);
			$this->article_image = $matches[0];
		}
		if(preg_match('$<div id="article-state">(.+)</div>$i', $out->mBodytext, $matches)){
			$out->mBodytext = preg_replace('$<div id="article-state">(.+)</div>$i', '', $out->mBodytext);
			$this->article_state = $matches[0];
		}
		*/
		
		//echo $this->article_summary; exit;
		
		//add the search form to every page. This just puts it inside the main article, which isn't ideal, but it does the job
		//$out->addWikiText('<div id="semantic-search">{{Special:RunQuery/Search/Plant_name}}</div>');
		
		//$srch = Parser::parse('{{Special:RunQuery/Search/Plant_name}}');
		//echo $srch; exit;
		//$out->setPageTitle('<em> TEST </em>');
		//echo '<pre>'; print_r($out); exit;
	}
	
	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;
		parent::setupSkinUserCss( $out );

		//$out->addModuleStyles( 'skins.practicalplants' );
		//$out->addScript('<script type="text/javascript" src="practicalplants/js/modernizr-1.7.min.js"></script>');
		//$out->addScript('<script type="text/javascript" src="practicalplants/js/jquery-1.7.1.min.js"></script>');
		//$out->addStyle( 'practicalplants/css/main.css', 'screen' );
		//$out->addStyle( 'practicalplants/css/print.css', 'print' );
		
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

		// Reverse horizontally rendered navigation elements
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
				array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
				array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
				array_reverse( $this->data['personal_urls'] );
		}

		$this->html( 'headelement' );
?>
<script type="text/javascript">
</script>
<div id="page-wrapper">
	<?php require(realpath(__DIR__.'/../../../library').'/Masthead.php');
	$masthead = new PracticalPlants_Masthead(array(
		'active_tab'=>'wiki',
		'search'=>'<form action="/wiki/index.php" id="searchform">
			<input type="hidden" name="title" value="Special:Search">
			<input name="search" title="Search Practical Plants [ctrl-option-f]" accesskey="f" id="searchInput" placeholder="Species/Taxonomy name or search term" class="search-field">			<input type="submit" name="go" value="Go" title="Go to a page with this exact name if exists" id="searchGoButton" class="searchButton">			<input type="submit" name="fulltext" value="Search" title="Search the pages for this text" id="mw-searchButton" class="searchButton">		</form>'
	));
	$masthead->output();
	?>
	
	<?php if($this->data['sitenotice']) { ?><div id="siteNotice"><?php $this->html('sitenotice') ?></div><?php } ?>
	<article id="main-entry" class="wiki-entry">
		<a id="beta-banner" href="/wiki/PracticalPlants:Beta">Practical Plants is in Beta!</a>
		<header id="page-header">
			<h1 id="article-title"><?php $this->html('title') ?></h1>
			<?php if($this->article_summary) echo $this->article_summary; ?>
			<div id="article-image-container"><?php if($this->article_image) echo $this->article_image; ?></div>
		</header>	
		<div id="after-header">
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
		<nav id="article-actions">
			<h2>Article Administration</h2>
			<div class="left">
			<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS' ) ); ?>
			</div>
			<div class="right">
			<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS') ); ?>
			</div>
		</nav>
		</div>
	</article>


	<nav id="menubar"<?php $this->html('userlangattributes')  ?> class="masthead-submenu">
		<h2>Navigation Links</h2>
		<div class="menu"><?php $this->renderNavigation( array( 'PERSONAL') ); $this->renderPortals($this->data['sidebar']); ?></div>
		
	
	</nav><!-- end of the left (by default at least) column -->
	
	
	<div class="clear"></div>
	<div id="footer"<?php $this->html('userlangattributes') ?>>
		<div class="footer-column" id="footer-about">
			<h3>About Practical Plants</h3>
			<p>Practical Plants is a plant database designed for <a href="/wiki/Permaculture">Permaculture</a> enthusiasts, <a href="/wiki/Agroforestry">Forest Gardeners</a>, Homesteaders, Farmers and anyone interested in <a href="/wiki/Organic">organic horticulture</a>.</p>
			<p>We believe sustainability must be more than a greenwash over the status quo. It must be a significant rethink of society, with sustainable agriculture as one of the most important components</p>
		</div>
		<div class="footer-column" id="footer-thanks">
			<h3>Our thanks to...</h3>
			<div class="thanks-to" id="thanks-tinymighty">
				<a href="http://tinymighty.com" class="thanks-logo"></a>
				<h4><a href="http://tinymighty.com">TinyMighty</a></h4>
				<p>Website designed, developed and generously hosted by TinyMighty.</p>
			</div>
			<div class="thanks-to" id="thanks-pfaf">
				<a href="http://pfaf.org" class="thanks-logo"></a>
				<h4><a href="http://pfaf.org">Plants For A Future</a></h4>
				<p>The fantastic database this project was forked from was created by PFAF (<a href="/wiki/PracticalPlants:PFAF">read more</a>).</p>
			</div>
			<div class="thanks-to" id="thanks-mediawiki">
				<a href="http://mediawiki.org" class="thanks-logo"></a>
				<h4><a href="http://mediawiki.org">MediaWiki</a> &amp; <a href="http://semantic-mediawiki.org">SMW</a></h4>
				<p>This website uses technology developed by the smart people behind MediaWiki and Semantic MediaWiki.</p>
			</div>
			<div class="thanks-to" id="thanks-cernunnos">
				<a href="http://cernunnos.es" class="thanks-logo"></a>
				<h4><a href="http://cernunnos.es">Cernunnos</a></h4>
				<p>This project is supported by the guys living at Cernunnos.</p>
			</div>
		</div>
		<div class="footer-column" id="footer-others">
			<?php $links = $this->getFooterLinks(); ?>
			<div id="footer-docs">
			<h3>Documents</h3>
			<ul>
			<?php foreach( $links['places'] as $link ): ?>
				<li><?php $this->html( $link ) ?></li>
			<?php endforeach; ?>
			</ul>
			</div>
			<div id="footer-info">
			<h3>Page Info</h3>
			<ul>
			<?php foreach( $links['info'] as $link ): ?>
				<li><?php $this->html( $link ) ?></li>
			<?php endforeach; ?>
			</ul>
			</div>
		</div>

	
	</div>
</div>

<?php
		$this->printTrail();
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
<div class="dropdown" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
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
	<?php			foreach( $this->getPersonalTools() as $key => $item ) { ?>
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


