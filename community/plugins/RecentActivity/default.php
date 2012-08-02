<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['RecentActivity'] = array(
   'Name' => 'RecentActivity',
   'Description' => "Show the recent activity in the sidebar.",
   'Version' => '0.1',
   'Author' => "Rémi Cieplicki",
   'AuthorEmail' => 'remouk@gmail.com',
   'AuthorUrl' => 'http://ledpong.com',
   'RegisterPermissions' => FALSE,
   'SettingsPermission' => FALSE
);

class RecentActivityPlugin extends Gdn_Plugin {
   
   public function Base_Render_Before(&$Sender) {
      $Controller = $Sender->ControllerName;
	  
	  if (!InArrayI($Controller, array('categoriescontroller', 'discussioncontroller', 'discussionscontroller', 'messagescontroller', 'profilecontroller'))) return; 
   
	  $RecentActivityModule = new RecentActivityModule($Sender);
	  $RecentActivityModule->GetData(10);
	  $Sender->addModule($RecentActivityModule);
   }
   
   public function Setup() {
      // No setup required
   }
}
