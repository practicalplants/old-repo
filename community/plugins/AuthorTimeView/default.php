<?php if (!defined('APPLICATION'))  exit();


// Define the plugin:
$PluginInfo['AuthorTimeView'] = array(
    'Name' => 'AuthorTimeView',
    'Description' => 'This plugin adds optional original Author and Time and optional View Count in Discussions Page.',
    'Version' => '1.2',
    'SettingsUrl' => '/dashboard/plugin/authortimeview',
    'HasLocale' => TRUE,
    'Author' => "Peregrine"
);

class AuthorTimeViewPlugin extends Gdn_Plugin {

   
    public function PluginController_AuthorTimeView_Create(&$Sender, $Args = array()) {
        $Sender->Title('Author Time view');
        $Sender->AddSideMenu('plugin/authortimeview');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.AuthorTimeView.Show_AuthorTime',
            'Plugins.AuthorTimeView.Show_Vcount'
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('atv-settings.php'));
    }
 
    public function DiscussionsController_BeforeDiscussionMeta_Handler(&$Sender) {
       
       
         if (C('Plugins.AuthorTimeView.Show_Vcount')) {
           $NumViews = $Sender->EventArguments['Discussion']->CountViews;
           echo "$NumViews View(s) | ";
           }
    }
  
  
    public function DiscussionsController_AfterCountMeta_Handler(&$Sender) {
     
      $Discussion = $Sender->EventArguments['Discussion'];
      if (C('Plugins.AuthorTimeView.Show_AuthorTime')) {
        $First = UserBuilder($Discussion, 'First');
        $Last = UserBuilder($Discussion, 'Last');

        if ($Discussion->LastCommentID != '') {
            echo '<span class="LastCommentBy">'.sprintf(T(' %1$s'), UserAnchor($First)).'</span>';
            echo '<span class="AuthorDate">'.Gdn_Format::Date($Discussion->FirstDate).'</span>';
            echo '<span class="LastCommentBy">'.sprintf(T('| Recent %1$s'), UserAnchor($Last)).'</span>';

        } else {
          
// leave space preceding Started in ' Started by' or it will be removed by locale definitions      
            echo '<span class="LastCommentBy">'.sprintf(T(' Started by %1$s'), UserAnchor($First)).'</span>';
        }
       }
} 
  public function CategoriesController_AfterCountMeta_Handler(&$Sender) {
        $this->DiscussionsController_AfterCountMeta_Handler($Sender);
    }

 public function CategoriesController_BeforeDiscussionMeta_Handler(&$Sender) {
       $this->DiscussionsController_BeforeDiscussionMeta_Handler($Sender);
    }
  
  
  
   public function Setup() {
        
    }

    public function OnDisable() {
     
    }

}

