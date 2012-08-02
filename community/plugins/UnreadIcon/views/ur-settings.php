<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>


<h1><?php echo Gdn::Translate('Unread Icon'); ?></h1>

<div class="Info"><?php echo Gdn::Translate('Unread Icon Options.'); ?></div>

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
                echo $this->Form->CheckBox(
                    'Plugins.UnreadIcon.Show_Unread', 'Show Unread Icon',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Check to show "unread icon" with each unread comment in individual discussion page'); ?>
            </td>
        </tr>
        
         <tr>
            <td>
                <?php
                echo $this->Form->CheckBox(
                    'Plugins.UnreadIcon.Show_Last', 'Show Last Icon',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Check to show "last message icon" with last comment in individual discussion page'); ?>
            </td>
        </tr>
        
          <tr>
            <td>
                <?php
                echo $this->Form->CheckBox(
                     'Plugins.UnreadIcon.Show_Recent', 'Show Recent Icon',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Check to show "recent icon" with each comment less than x hours old'); ?>
            </td>
        </tr>   
            
            
            
            
              <tr>
            <td>
                <?php
                
                 $Hours[0] = 5;
                
                 for($i = 1; $i < 24; $i++) {
                 $Hours[$i] = $i;
                 }
                
                
                
                echo $this->Form->DropDown('Plugins.UnreadIcon.Show_Hours',$Hours);
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Select recent discussion less than "?" hours old'); ?>
            </td>
        </tr>



</table>

<?php echo $this->Form->Close('Save');


