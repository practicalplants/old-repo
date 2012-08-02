<!DOCTYPE html>
<html>
<head>
   <?php $this->RenderAsset('Head'); ?>
  <script src="/community/js/library/jquery.autocomplete.js"></script>
  <script src="/resources/js/search-autocomplete.js"></script>
</head>
<body id="<?php echo $BodyIdentifier; ?>" class="<?php echo $this->CssClass; ?>">
<?php require(realpath(PATH_ROOT.'/../library').'/Masthead.php');
$masthead = new PracticalPlants_Masthead(array('active_tab'=>'community'));
$masthead->output();
?>
<div id="Frame">
  <div id="Head" class="masthead-submenu">
    <div class="Row">
      <div class="SiteSearch"><?php
      		$Form = Gdn::Factory('Form');
      		$Form->InputPrefix = '';
      		echo 
      			$Form->Open(array('action' => Url('/search'), 'method' => 'get')),
      			$Form->TextBox('Search', array('placeholder'=>'Forum search')),
      			$Form->Button('Go', array('Name' => '')),
      			$Form->Close();
      	?></div>
        <?php
		      $Session = Gdn::Session();
				if ($this->Menu) {
					$this->Menu->AddLink('Dashboard', T('Dashboard'), '/dashboard/settings', array('Garden.Settings.Manage'));
					$this->Menu->AddLink('Dashboard', T('Users'), '/user/browse', array('Garden.Users.Add', 'Garden.Users.Edit', 'Garden.Users.Delete'));
					//$this->Menu->AddLink('Activity', T('Activity'), '/activity');
					if ($Session->IsValid()) {
						$Name = $Session->User->Name;
						$CountNotifications = $Session->User->CountNotifications;
						if (is_numeric($CountNotifications) && $CountNotifications > 0)
							$Name .= ' <span class="Alert">'.$CountNotifications.'</span>';

	                     if (urlencode($Session->User->Name) == $Session->User->Name)
	                        $ProfileSlug = $Session->User->Name;
	                     else
	                        $ProfileSlug = $Session->UserID.'/'.urlencode($Session->User->Name);
						$this->Menu->AddLink('User', T('Profile'), '/profile/'.$ProfileSlug, array('Garden.SignIn.Allow'), array('class' => 'UserNotifications'));
						//$this->Menu->AddLink('SignOut', T('Sign Out'), SignOutUrl(), FALSE, array('class' => 'NonTab SignOut'));
					} else {
						$Attribs = array();
						if (SignInPopup() && strpos(Gdn::Request()->Url(), 'entry') === FALSE)
							$Attribs['class'] = 'SignInPopup';
							
						$this->Menu->AddLink('Entry', T('Sign In'), SignInUrl($this->SelfUrl), FALSE, array('class' => 'NonTab'), $Attribs);
					}
					echo $this->Menu->ToString();
				}
			?>
    </div>
  </div>
  <div id="Body">
    <div class="Row">
      <div class="Column PanelColumn" id="Panel">
         <?php $this->AddModule('MeModule'); ?>
         <?php $this->RenderAsset('Panel'); ?>
      </div>
      <div class="Column ContentColumn" id="Content"><?php $this->RenderAsset('Content'); ?></div>
    </div>
  </div>
  <div id="Foot">
    <div class="Row">
      <a href="{vanillaurl}" class="PoweredByVanilla">Powered by Vanilla</a>
      <?php	$this->RenderAsset('Foot'); ?>
    </div>
  </div>
</div>
<?php $this->FireEvent('AfterBody'); ?>
</body>
</html>