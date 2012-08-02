<?php if (!defined('APPLICATION')) exit();
/**
* # My Profile #
* 
* ### About ###
* Adds an extended and extensible user profile. Uses a simple Yaml Meta Spec to model the desired profile.
* 
* ### Sponsor ###
* Special thanks to ryanopaz & Vrazon (http://vrazon.com) for making this happen.
*/

/*
 * chagelog:
 * v0.1.3b:Tue Mar 27 16:39:11 BST 2012
 * - PAAMAYIM_NEKUDOTAYIM fix
 * v0.1.4b:Thu Mar 29 13:50:12 BST 2012
 * - Changes sponsored by to ryanopaz & Vrazon (http://vrazon.com) 
 * - type:standard to list non extended fields (order how want) other params than name are ignored
 * - requiredwith (field that a reliant on other fields)
 * - params (passed to link/label vsprintf)
 * - labeldefault (locale like vsprintf format string can be overridden by locale MyMeta.Field.Label
 * v0.1.5b:Thu Mar 29 16:04:47 BST 2012
 * - more amicable link/label default
 * v0.1.6b:Sun Apr  8 22:29:46 BST 2012
 * - formatting for label default
 * v0.1.7b:Sun Apr  8 22:29:46 BST 2012
 * - strftime rather than Gnd_Format::Date
 * - Date.DefaultFormat
 * v0.1.9b:Thu Jun  7 23:57:34 BST 2012
 * - view permissions fix
 */
$PluginInfo['MyProfile'] = array(
	'Name' => 'MyProfile',
	'Description' => 'Adds an extended and extensible user profile. Uses a simple Yaml Meta Spec to model the desired profile.',
	'Version' => '0.1.9b',
	'Author' => "Paul Thomas",
	'AuthorEmail' => 'dt01pqt_pt@yahoo.com	',
	'AuthorUrl' => 'http://vanillaforums.org/profile/x00'
);

Gdn::FactoryInstall('sfYaml','sfYaml',dirname(__FILE__).DS.'sfYaml'.DS.'lib'.DS.'sfYaml.php',Gdn::FactorySingleton);


class MyProfile extends Gdn_Plugin {

	public function ProfileController_AddProfileTabs_handler($Sender) {
		$Sender->AddProfileTab('MyProfile', "/profile/myprofile/".$Sender->User->UserID."/".Gdn_Format::Url($Sender->User->Name), 'MyProfile', sprintf(T('About %s'),$Sender->User->Name));
	}

	public function ProfileController_AfterAddSideMenu_Handler($Sender) {
		if(!Gdn::Session()->CheckPermission('Garden.Users.Edit') && $Sender->User->UserID!==Gdn::Session()->UserID) return;
		$SideMenu = $Sender->EventArguments['SideMenu'];
		$SessionUserID = Gdn::Session()->UserID;
		if ($Sender->User->UserID == $SessionUserID) {
			$SideMenu->AddLink('Options', T('My Profile Edit'), '/profile/myprofileedit/'.$Sender->User->UserID.'/'.Gdn_Format::Url($Sender->User->Name), FALSE, array('class' => 'Popup'));
		} else {
			$SideMenu->AddLink('Options', T('My Profile Edit'), '/profile/myprofileedit/'.$Sender->User->UserID.'/'.Gdn_Format::Url($Sender->User->Name), 'Garden.Users.Edit', array('class' => 'Popup'));
		}
	}
	public function ProfileController_MyProfile_Create($Sender, $Args) {
		$sfYaml = Gdn::Factory('sfYaml');
		if(file_exists(dirname(__FILE__).DS.'mymeta.yml')){
			$Meta = $sfYaml->load(dirname(__FILE__).DS.'mymeta.yml');
			$Sender->SetData('Example',false);
		}else{
			$Meta = $sfYaml->load(dirname(__FILE__).DS.'mymeta.yml.example');
			$Sender->SetData('Example',true);
		}
	   $Sender->UserID = ArrayValue(0, $Sender->RequestArgs, '');
	   $Sender->UserName = ArrayValue(1, $Sender->RequestArgs, '');
	   $Sender->GetUserInfo($Sender->UserID, $Sender->UserName);
	   $Sender->SetTabView('MyProfile', dirname(__FILE__).DS.'views'.DS.'view.php', 'Profile', 'Dashboard');
	   $Data = UserModel::GetMeta($Sender->UserID,'MyMeta.%','MyMeta.');	   
	   foreach($Meta['MyMeta'] As $MetaI => $MetaV)
			$MyMeta[$MetaI]=$Data[$MetaI];
       $Sender->AddCssFile('myprofile.css','plugins/MyProfile/');
       $Sender->AddJsFile('picsa.js','plugins/MyProfile/'); 
       $Sender->AddJsFile('jflickrfeed.js','plugins/MyProfile/'); 
	   $Sender->SetData('MyMeta',$MyMeta);
	   $Sender->SetData('MetaSpec',$Meta['MyMeta']);
	   $Sender->Render();
	}

	public function ProfileController_MyProfileEdit_Create($Sender, $Args) {
		$sfYaml = Gdn::Factory('sfYaml');
		$Sender->UserID = ArrayValue(0, $Args, '');
		$Sender->UserName = ArrayValue(1, $Args, '');
		$Sender->GetUserInfo($Sender->UserID, $Sender->UserName);
		$SessionUserID = Gdn::Session()->UserID;
		if ($Sender->User->UserID != $SessionUserID) {
			$Sender->Permission('Garden.Users.Edit');
			$MyMetaUserID = $Sender->User->UserID;
		} else {
			$MyMetaUserID = $SessionUserID = Gdn::Session()->UserID;
		}
		
		if(file_exists(dirname(__FILE__).DS.'mymeta.yml')){
			$Meta = $sfYaml->load(dirname(__FILE__).DS.'mymeta.yml');
			$Sender->SetData('Example',false);
		}else if(file_exists(dirname(__FILE__).DS.'mymeta.yml.example')){
			$Meta = $sfYaml->load(dirname(__FILE__).DS.'mymeta.yml.example');
			$Sender->SetData('Example',true);
		}
		$Sender->Form = new Gdn_Form();
		$ValidationFailed=false;
		if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
			$Sender->Form->SetData($Sender->MyProfile);
		} else {
			
			$Data = $Sender->Form->FormValues();
			$Validation = new Gdn_Validation();	
			foreach($Data As $DataI=>$DataV) { 
				$Field = $Meta['MyMeta'][$DataI];
				if(GetValue('required',$Field)){
					$Validation->ApplyRule($DataI,'Required', sprintf(T('%s is required'),$Field['name']));
				}
				
				foreach(GetValue('requiredwith',$Field) As $RequiredWith){
					if(!GetValue($RequiredWith,$Datarequired)){
						$Validation->ApplyRule($RequiredWith,'Required', sprintf(T('%s is required with %s'),$Meta['MyMeta'][$RequiredWith]['name'],$Field['name']));
					}
				}
				if(empty($DataV)) continue;
				if($V = GetValue('validate',$Field)){
					
					if(strpos($V,'Validate')===0){//Begins with
						$V = substr($V,8);
						if(function_exists($V))
							$Validation->AddRule($V,'function:'.$V);							
						else if(function_exists($V))
							$Validation->AddRule($V,'function:Validate'.$V);
					}
		
					$Validation->ApplyRule($DataI,$V, sprintf(T('%s not valid'),$Field['name']));
				}
				if($R = GetValue('validateregex',$Field)){
					$Validation->AddRule($DataI,'regex:`^'.$R.'$`i');
					$Validation->ApplyRule($DataI,$DataI, sprintf(T('%s not valid'),$Field['name']));
				}
				if($M = GetValue('maxchar',$Field)){
					$Validation->AddRule('MaxLen'.$DataI,'regex:`^.{0,'.$M.'}$`is');
					$Validation->ApplyRule($DataI,'MaxLen'.$DataI, sprintf(T('%s not cannot be longer than %s chars'),$Field['name'],$M));
				}
				
			}
			$Validation->Validate($Data);
			if (count($Validation->Results()) == 0) {
				$MyMeta = array_intersect_key($Data,$Meta['MyMeta']);
				UserModel::SetMeta($MyMetaUserID,$MyMeta,'MyMeta.');
			}else{
				$ValidationFailed=true;
			}
			$Sender->Form->SetValidationResults($Validation->Results());
		}
		
		if(!$ValidationFailed){
			$Data = UserModel::GetMeta($MyMetaUserID,'MyMeta.%','MyMeta.');
		}
	    $MyMeta = array_intersect_key($Data,$Meta['MyMeta']);
		$Sender->SetData('Fields',$Meta['MyMeta']);
		$Sender->SetData('MyMeta',$MyMeta);
		$Sender->View = dirname(__FILE__).DS.'views'.DS.'edit.php';
		$Sender->Render();
	}
	
	public function Base_Render_Before($Sender,$Args){
		if(strtolower($Sender->PageName())!=='profile') return;
		
		$Sender->AddCssFile('myprofileedit.css','plugins/MyProfile/');
	}

    /* setup spec*/
    
	public function Base_BeforeDispatch_Handler($Sender){
		if(C('Plugins.MyProfile.Version')!=$this->PluginInfo['Version'])
			$this->Structure();
	}
    
	public function Setup() {
		$this->Structure();
	}

	public function Structure() {
		//Save Version for hot update

		SaveToConfig('Plugins.MyProfile.Version', $this->PluginInfo['Version']);
	}
    /* setup spec*/
}
