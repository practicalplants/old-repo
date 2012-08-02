<?php if (!defined('APPLICATION')) exit(); ?>
<h2><?php echo T('My Profile Data'); ?></h2>
<?php
// Initialize the Form
echo $this->Form->Open();
echo $this->Form->Errors();
if($this->Data['Example']) { 
?>
	<div class="Warn">
		<?php echo T("Please rename mymeta.yml.example to mymeta.yml"); ?>
	</div>
<?php } 
$Hidden=array();

?>

<ul class="MyProfile">
<?php foreach($this->Data['Fields'] As $FieldI=>$Field) { 
	switch($Field['type']){
		case 'standard':
			$this->Form->AddHidden($FieldI,'standard');
			$Hidden[] = $this->Form->Hidden($FieldI,array('value'=>'standard'));
			break;
		case 'text':
		?>
			 <li>
			  <?php
				echo $this->Form->Label($Field['name'],$FeildI);
				echo $this->Form->TextBox($FieldI,array('Value'=>GetValue($FieldI,$this->Data['MyMeta'],'')));
				echo $Field['eg']?'<div class="eg">'.T('e.g: ').$Field['eg'].'</div>':''; 
				echo $Field['urlformat']?'<div class="prefix">'.T('format: ').$Field['urlformat'].'</div>':'';
				echo $Field['hint']?'<div class="hint">'.T('hint: ').$Field['hint'].'</div>':'';
			  ?>
			</li>
			<?php
			break;
		case 'textbox':
		?>
			 <li>
			  <?php
				 echo $this->Form->Label($Field['name'],$FieldI);
				 echo $this->Form->TextBox($FieldI,array('Multiline'=>true,'class'=>'Multiline','Value'=>GetValue($FieldI,$this->Data['MyMeta'])));
			  ?>
			</li>
			<?php
			break;
		case 'date':
		?>
			 <li>
			  <?php
				 echo $this->Form->Label($Field['name'],$FieldI);
				 $this->Form->SetValue($FieldI,GetValue($FieldI,$this->Data['MyMeta']));
				 echo $this->Form->Date($FieldI);
			  ?>
			</li>
			<?php
			break;
	}
}
?>
</ul>
<?php
if(!empty($Hidden))
	echo join('',$Hidden);
// Close the form
echo $this->Form->Close('Save'); 
?>
