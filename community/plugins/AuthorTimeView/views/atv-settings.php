<?php if (!defined('APPLICATION')) exit();
echo $this->Form->Open();
echo $this->Form->Errors();
?>


<h1><?php echo Gdn::Translate('Author Time View'); ?></h1>

<div class="Info"><?php echo Gdn::Translate('Author Time View Count Options.'); ?></div>

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
                    'Plugins.AuthorTimeView.Show_AuthorTime', 'Show Author Time',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Check to show original Author and original Time in Discussions page'); ?>
            </td>
        </tr>

          <tr>
            <td>
                <?php
                echo $this->Form->CheckBox(
                    'Plugins.AuthorTimeView.Show_Vcount', 'Show View Count',
                    array('value' => '1', 'selected' => 'selected')
                );
                ?>
            </td>
            <td class="Alt">
                <?php echo Gdn::Translate('Check to show view count per discussion in Discussions page'); ?>
            </td>
        </tr>   
            
</table>

<?php echo $this->Form->Close('Save');


