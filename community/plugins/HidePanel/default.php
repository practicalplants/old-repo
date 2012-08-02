<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['HidePanel'] = array(
   'Name' => 'Hide Panel',
   'Description' => "Add Toggle slideup and down specific Panels.  Default setting is to allow toggle on Bookmarks Panel and Category Panel and In This Discussions Panel.  Modify css and js to add more panels.  Also, there is a setting option to apply this to all discussions on the discussions pages.",
   'Version' => '1.2',
   'Author' => "Peregrine",
   'SettingsUrl' => '/dashboard/plugin/hidepanel'
);


class HidePanelPlugin extends Gdn_Plugin {
	
	public function Base_Render_Before(&$Sender) {
	        // uncomment to find the url of the page you want to locate
			//  echo "The url for this page is:" . Url()
            $showdiscussion = FALSE;
            if (preg_match('|/discussion/|', Url())) {
		           $showdiscussion = TRUE;
		        }
		
		 if  ((Url() == C('Plugins.HidePanel.Page1'))  
		     || (Url() == C('Plugins.HidePanel.Page2'))
		     || (Url() == C('Plugins.HidePanel.Page3'))             
		     || (($showdiscussion)  &&  C('Plugins.HidePanel.AllDisc'))   
		              )  {
		    
		     
		      if (strtolower(C('Plugins.HidePanel.HideToggle')) == "hide") { 
		         $Sender->AddCssFile('hpanel.css', 'plugins/HidePanel');
		         } else {
		         $Sender->AddJsFile('/plugins/HidePanel/js/togglepanel.js');
                 $Sender->AddCssFile('togglepanel.css', 'plugins/HidePanel');
              }
       	  
  
          }

}

 public function PluginController_HidePanel_Create(&$Sender, $Args = array()) {

        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.HidePanel.HideToggle',
            'Plugins.HidePanel.AllDisc',
            'Plugins.HidePanel.Page1',
            'Plugins.HidePanel.Page2',
            'Plugins.HidePanel.Page3',
       
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('hpan-settings.php'));
    }



 public function Setup() {
        
    }

}
