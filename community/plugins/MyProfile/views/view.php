<?php if (!defined('APPLICATION')) exit();
if($this->Data['Example']) { 
?>
	<div class="Alert">
		<?php echo T("Please rename mymeta.yml.example to mymeta.yml"); ?>
	</div>
<?php } ?>
<div class="MyProfile Box About">
<table>
<?php 
foreach($this->Data['MyMeta'] As $MetaI => $MetaV){
	if(!$MetaV) continue;
	if('standard'==$this->Data['MetaSpec'][$MetaI]['type']){
		switch($MetaI){
			case 'UserPhoto':
				$MetaV = UserPhoto($this->User,array('class'=>'ProfilePhotoMedium'));
				break;
			case 'UserName':
				$MetaV = Gdn_Format::Text($this->UserName);
				break;
			case 'Email':		
				if ($this->User->ShowEmail == 1 || Gdn::Session()->CheckPermission('Garden.Registration.Manage')) {
					$MetaV = Gdn_Format::Email($this->User->Email);
				}else{
					continue;
				}
				break;
			case 'DateFirstVisit':
				$MetaV = Gdn_Format::Date($this->User->DateFirstVisit);
				break;
			case 'CountVisits':
				$MetaV = number_format($this->User->CountVisits);
				break;
			case 'DateLastActive':
				$MetaV = Gdn_Format::Date($this->User->DateLastActive);
				break;
			case 'Roles':
				$MetaV = implode(', ', $this->Roles);
				break;
			case 'RegisterIP':
				if (Gdn::Session()->CheckPermission('Garden.Moderation.Manage')){
					$IP = IPAnchor($this->User->InsertIPAddress);
					$MetaV = $IP ? $IP : T('n/a');
				}else{
					continue;
				}
				break;
			case 'LastIP':
				if (Gdn::Session()->CheckPermission('Garden.Moderation.Manage')){
					$IP = IPAnchor($this->User->LastIPAddress);
					$MetaV = $IP ? $IP : T('n/a');
				}else{
					continue;
				}
				break;			
			default:
				continue;
				break;
		}
	}
	if(GetValue('hide',$this->Data['MetaSpec'][$MetaI])) continue;
	$Params = array();
	foreach(GetValue('params',$this->Data['MetaSpec'][$MetaI]) As $Param)
		if(GetValue($Param,$this->Data['MyMeta']))
			$Params[] = $this->Data['MyMeta'][$Param];

	switch($this->Data['MetaSpec'][$MetaI]['type']){
		case 'text':
			$MetaV= Gdn_Format::Text($MetaV);
			$Picasa='';
			$Flickr='';
			if($this->Data['MetaSpec'][$MetaI]['social']=='picasa')
				$Picasa = '<input type="hidden" class="picsa" value="'.$MetaV.'" />';
			if($this->Data['MetaSpec'][$MetaI]['social']=='flickr')
				$Flickr = '<input type="hidden" class="flickr" value="'.$MetaV.'" />';
			if(ValidateEmail($MetaV))
				$MetaV=Email($MetaV);
			else if(ValidateWebAddress($MetaV)&& preg_match('`\.(jpg|jpeg|gif|png)$`i',$MetaV))
				$MetaV=Anchor(Img($MetaV,$MetaV),$MetaV);
			else if(ValidateWebAddress($MetaV)){
				$LabelDefault = Gdn_Format::Text(GetValue('labeldefault',$this->Data['MetaSpec'][$MetaI]));
				$Params = (array_merge(array($this->User->Name,$this->Data['MetaSpec'][$MetaI]['name']),$Params));
				$MetaV= '<a href="'.$MetaV.'">'.vsprintf(T('MyMeta.'.$MetaI.'.Label',$LabelDefault?$LabelDefault:'%2$s'),$Params).'</a>';
			}else if($this->Data['MetaSpec'][$MetaI]['urlformat']
					&& ValidateWebAddress($this->Data['MetaSpec'][$MetaI]['urlformat'])){
				$LabelDefault = Gdn_Format::Text(GetValue('labeldefault',$this->Data['MetaSpec'][$MetaI]));
				$Params = (array_merge(array($this->User->Name,$this->Data['MetaSpec'][$MetaI]['name']),$Params));
			    $MetaV = '<a href="'.str_replace('[id]',$MetaV,$this->Data['MetaSpec'][$MetaI]['urlformat']).'">'.vsprintf(T('MyMeta.'.$MetaI.'.Label',$LabelDefault?$LabelDefault:'%2$s'),$Params).'</a>';	
			}
			$MetaV=$Flickr.$Picasa.$MetaV;
			break;
		case 'textbox':
			$MetaV= Gdn_Format::Auto($MetaV);
			break;
		case 'date':
			$MetaV=strftime(T('Date.DefaultFormat','%B %e, %Y'), strtotime($MetaV));
			break;
	}
?>
<tr>
	<td class="Name">
		<?php echo T($this->Data['MetaSpec'][$MetaI]['name']) ?>
	</td>
	<td>
		<?php echo $MetaV; ?>
	</td>
</tr>
<?php } ?>
</table>
</div>

