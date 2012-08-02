<?php if (!defined('APPLICATION')) exit();

class PracticalPlantsThemeHooks implements Gdn_IPlugin {
	
   public function Setup() {
		return TRUE;
   }
	
	public function OnLoad(){
	}
	
   public function OnDisable() {
      return TRUE;
   }
	
}