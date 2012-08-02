<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>


<h1><?php echo Gdn::Translate('Hide Panel'); ?></h1>

<div class="Info"><?php echo Gdn::Translate('Add Pages to hide Panel. e.g. /vanilla/categories/testing and then modify /plugins/HidePanel/design/hpanel.css to hide panels or element on the pages entered below'); ?></div>

<table class="AltRows">
    <thead>
        <tr>
            <th><?php echo Gdn::Translate('Option'); ?></th>
            <th class="Alt"><?php echo Gdn::Translate('Description'); ?></th>
        </tr>
    </thead>
    <tbody>
     
       <tr>
            <td>
                <?php
            
          
          echo $this->Form->CheckBox('Plugins.HidePanel.AllDisc'); 
            
      
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Do you want this to apply to each and every discussion?'); ?>
            </td>
        </tr>
     
     
     
     
        <tr>
     
      <tr>
            <td>
                <?php
            
          
          echo $this->Form->RadioList('Plugins.HidePanel.HideToggle', array('toggle' => 'toggle', 'hide' => 'hide')); 
            
      
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Choose Hide Panel or TogglePanel'); ?>
            </td>
        </tr>
     
     
     
     
        <tr>
            <td>
                <?php
                echo $this->Form->TextBox('Plugins.HidePanel.Page1');
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Add the location of the first page to hide panel on'); ?>
           
           
            </td>
        </tr>
        
        <tr>
            <td>
                <?php
                echo $this->Form->TextBox('Plugins.HidePanel.Page2');
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Add the location of the second page to hide panel on'); ?>
            </td>
        </tr>
       
       
      <tr>
            <td>
                <?php
                echo $this->Form->TextBox('Plugins.HidePanel.Page3');
                ?>
            </td>
            <td class="Alt">
                 <?php echo Gdn::Translate('Add the location of the third page to hide panel on'); ?>
            </td>
        </tr>
        
       
</table>
   
<?php echo $this->Form->Close('Save');


