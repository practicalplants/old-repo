<?php if (!defined('APPLICATION')) exit();

$PluginInfo['MoreMessagesPositions'] = array(
	'Name' => 'More Messages Positions',
	'Description' => 'Adds more positions to the dashboard Appearance/Messages interface. Knowledge and editing of your theme template may be required to add support for custom positions.',
	'Version' 	=>	 '1.0.1',
	'MobileFriendly' => TRUE,
	'Author' 	=>	 "Matt Sephton",
	'AuthorEmail' => 'matt@gingerbeardman.com',
	'AuthorUrl' =>	 'http://www.gingerbeardman.com',
	'License' => 'GPL v2',
);

// 
// To add a New Messages Position:
// 
// 1. Duplicate one of the lines above
// 2. Change MMP_AfterBanner to a new short name (there is a 20 character limit)
// 3. Duplicate a display function
// 4. Change RenderAsset line to refer to your new message short name
// 5. Change the event hook, eg. Base_BeforeSidebar_Handler might become Base_AfterBody_Handler
// 

class MoreMessagesPositions implements Gdn_IPlugin {
	
	/**
	 * Add new positions to dashboard
	 */
	public function MessageController_AfterGetAssetData_Handler(&$Sender) {
		$Sender->EventArguments['AssetData']['MMP_BeforeSidebar'] = 'Above Sidebar';
		$Sender->EventArguments['AssetData']['MMP_AfterBanner'] = 'Below Banner';
	}
	
	/**
	 * Display message: MMP_BeforeSidebar
	 */
	public function Base_BeforeSidebar_Handler($Sender) {
		$Sender->RenderAsset('MMP_BeforeSidebar');
	}

	/**
	 * Display message: MMP_AfterBanner
	 */
	public function Base_AfterBanner_Handler($Sender) {
		$Sender->RenderAsset('MMP_AfterBanner');
	}

	public function Setup() {
		return TRUE;
	}
}