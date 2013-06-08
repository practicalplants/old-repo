<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['AutoBookmark'] = array(
   'Name' => 'AutoBookmark',
   'Description' => 'Automatically bookmarks a discussion when a user submits a comment',
   'Version' => '1.0',
   'Author' => "Ryder Ross",
   'AuthorEmail' => 'ryross@gmail.com',
   'AuthorUrl' => 'http://www.ryderross.com/'
);
class AutoBookmarkPlugin extends Gdn_Plugin {
	
	public function PostController_AfterCommentSave_Handler($Sender) {

		$Session = Gdn::Session();
		$Discussion = NULL;

		$isBookmarked = $Sender->DiscussionModel->SQL
			->Select('UserID')
			->From('UserDiscussion')
			->Where('DiscussionID', $Sender->Discussion->DiscussionID)
			->Where('Bookmarked', '1')
			->Where('UserID', $Session->UserID)
			->Get();

		if ($isBookmarked->NumRows() == 0) {
			$State = $Sender->DiscussionModel->BookmarkDiscussion($Sender->Discussion->DiscussionID, $Session->UserID, $Discussion);
			// Update the user's bookmark count
			$CountBookmarks = $Sender->DiscussionModel->SetUserBookmarkCount($Session->UserID);
		}
	}	

	public function Setup() {
		//nothing
	}
}

