<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-ca">
<head>
   <?php $this->RenderAsset('Head'); ?>
</head>
<body id="<?php echo $BodyIdentifier; ?>" class="<?php echo $this->CssClass; ?>">
	<nav id="masthead"><div id="logo"><a href="/wiki/" id="logo-image"></a><h1><a href="/wiki/"><em>Practical</em> Plants</a></h1></div>
		<ul class="tabs">
		<li><a href="/wiki/">Wiki</a></li>
		<li class="active"><a href="/community/">Community</a></li>
		</ul>
		<div id="wiki-search">
			<form action="/wiki/index.php" id="searchform">
				<input type="hidden" name="title" value="Special:Search">
				<input name="search" title="Search Practical Plants [ctrl-option-f]" accesskey="f" id="searchInput" placeholder="Species/Taxonomy name or search term" class="search-field autocompleteInput ui-autocomplete-input" autocompletesettings="All taxonomies" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">			<input type="submit" name="go" value="Go" title="Go to a page with this exact name if exists" id="searchGoButton" class="searchButton">			<input type="submit" name="fulltext" value="Search" title="Search the pages for this text" id="mw-searchButton" class="searchButton">		</form>
		</div>
	</nav>
   <div id="Frame">
      <div id="Head">
			<h1><?php echo Anchor(C('Garden.Title').' '.Wrap(T('Visit Site')), '/'); ?></h1>
         <div class="User">
            <?php
			      $Session = Gdn::Session();
					if ($Session->IsValid()) {
						$this->FireEvent('BeforeUserOptionsMenu');
						
						$Name = $Session->User->Name;
						$CountNotifications = $Session->User->CountNotifications;
						if (is_numeric($CountNotifications) && $CountNotifications > 0)
							$Name .= Wrap($CountNotifications);
							
						echo Anchor($Name, '/profile/'.$Session->User->UserID.'/'.$Session->User->Name, 'Profile');
						echo Anchor(T('Sign Out'), SignOutUrl(), 'Leave');
					}
				?>
         </div>
      </div>
      <div id="Body">
         <div id="Panel">
            <?php
            $this->RenderAsset('Panel');
            ?>
         </div>
         <div id="Content"><?php $this->RenderAsset('Content'); ?></div>
      </div>
      <div id="Foot">
			<?php
				$this->RenderAsset('Foot');
				echo '<div class="Version">Version ', APPLICATION_VERSION, '</div>';
				echo Wrap(Anchor(Img('/applications/dashboard/design/images/logo_footer.png', array('alt' => 'Vanilla Forums')), C('Garden.VanillaUrl')), 'div');
			?>
		</div>
   </div>
	<?php $this->FireEvent('AfterBody'); ?>
</body>
</html>