<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['UnreadIcon'] = array(
    'Name' => 'Unread Icon',
    'Description' => 'Inserts unread icon in front of unread comments and the last comment within indivdual discussions and Recent Icon for messages less than X hours old',
    'Version' => '1.2.3',
    'RequiredApplications' => array('Vanilla' => '2.0.17.8'),
    'RequiredTheme' => FALSE,
    'RequiredPlugins' => FALSE,
    'HasLocale' => FALSE,
    'SettingsUrl' => '/dashboard/plugin/unreadicon',
    'RegisterPermissions' => array('Plugins.UnreadIcon.Manage'),
    'Author' => "Peregrine"
);

class UnreadIcon extends Gdn_Plugin {

 
    public function PluginController_UnreadIcon_Create(&$Sender, $Args = array()) {
        $Sender->Title('Unread Icon');
        $Sender->AddSideMenu('plugin/authortimeview');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.UnreadIcon.Show_Unread',
            'Plugins.UnreadIcon.Show_Last',
            'Plugins.UnreadIcon.Show_Recent',
            'Plugins.UnreadIcon.Show_Hours'
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }

        $Sender->Render($this->GetView('ur-settings.php'));
    }

    protected $_Comids;

    public function GetData($DiscussionID, $Limit = 200) {

        $SQL = Gdn::SQL();
        $this->_ComIds = $SQL
                        ->Select('CommentID')
                        ->From('Comment')
                        ->Where('DiscussionID', $DiscussionID)
                        ->Get()->ResultArray();
        return $this->_ComIds;
    }

    protected $CommmentIdArray = Array();
    protected $lid;

    public function CacheUnread($CommmentIdArray) {
        $this->CommmentIdArray = $CommmentIdArray;
    }

    public function GetCacheUnread() {
        return $this->CommmentIdArray;
    }

    public function CacheLastDid($lid) {
        $this->lid = $lid;
    }

    public function GetCacheLastDid() {
        return $this->lid;
    }


    protected $unread;
    protected $totcount;
    protected $totdelete;

    public function DiscussionController_BeforeDiscussion_Handler($Sender) {

        // get the number of unread comments, last comment id
        $unread = $Sender->Discussion->CountComments - $Sender->Discussion->CountCommentWatch;
        $lid = $Sender->Discussion->LastCommentID;

        $this->CacheLastDid($lid);

        $this->lid = $lid;

        // get all comment ids for discussion

        $result = $this->GetData($Sender->Discussion->DiscussionID, $Limit = 300);

        $CommmentIdArray[0] = 0;

        $arrsize = count($result);

        for ($x = 0; $x < $arrsize; $x++) {
            $CommmentIdArray[$x] = $result[$x]['CommentID'];
        }

        rsort($CommmentIdArray);

        $totcount = count($CommmentIdArray);
        $todelete = $totcount - $unread;

        while ($todelete-- > 0) {
            array_pop($CommmentIdArray);
        }

        $this->CacheUnread($CommmentIdArray);
    }

    protected $cid;
    protected $recentTime;
    protected $insertTime;
    protected $showHours;
    public function DiscussionController_BeforeCommentMeta_Handler($Sender) {

        $CommmentIdArray = $this->GetCacheUnread();
        $lid = $this->GetCacheLastDid();


        $this->SessionInfo = Gdn::Session();
        $this->userID = $this->SessionInfo->UserID;

        $cid = $Sender->EventArguments['Comment']->CommentID;

        // display last posted icon
        if (($lid == $cid)  && (C('Plugins.UnreadIcon.Show_Last'))) {
            echo sprintf(' <img src="%s" class="LastIcon" title="last posted message in discussion" alt="last recent message in discussion" />', $this->GetWebResource('img/last-icon.png', FALSE, TRUE), $Key);
        }


        if (C('Plugins.UnreadIcon.Show_Recent')) {
        $showHours =  (C('Plugins.UnreadIcon.Show_Hours'));
        // display recently posted icon
        $recentTime = time() - ($showHours * 3600 );
        $insertTime = strtotime($Sender->EventArguments['Comment']->DateInserted);

        if   ($recentTime < $insertTime) {
            echo sprintf(' <img src="%s" class="RecentIcon" title="recent comment" alt="recent comment" />', $this->GetWebResource('img/recent-yellow.png', FALSE, TRUE), $Key);
        }
        }
        
         if (C('Plugins.UnreadIcon.Show_Unread')) {
        // display unread icon
        if (($this->userID > 0) && (in_array($cid, $CommmentIdArray))) {

            echo sprintf(' <img src="%s" class="UnreadIcon" title="unread comment"  alt="unread comment" />', $this->GetWebResource('img/unread-icon.png', FALSE, TRUE), $Key);
        }
       } 
   
    }

   
   
}
       
       
      




