<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['CategoryButtons'] = array(
	'Name' => 'Category Buttons',
	'Description' => 'Replaces the category dropdown with selectable buttons',
	'Version' => '0.1',
	'Author' => 'Dave Stewart',
	'AuthorEmail' => 'dave@davestewart.co.uk',
	'AuthorUrl' => 'http://www.davestewart.co.uk',
);

class CategoryButtons implements Gdn_IPlugin
{

	public function PostController_Render_Before(&$Sender)
	{
		$this->_init($Sender);
	}
	
	protected function _init($Sender)
	{
		$Sender->AddCSSFile('/plugins/CategoryButtons/styles.css');
		$Sender->AddJsFile('/plugins/CategoryButtons/init.js');
	}
	
	public function Setup()
	{
		// We don't have to setup this plugin
	}
}