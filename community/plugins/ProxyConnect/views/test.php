<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T('Proxy Connect Test'); ?></h1>
<div class="Info">
   <?php echo T('ProxyConnect.TestIntro', "This interface will allow you to test your configuration and ensure that 
   ProxyConnect is working."); ?>
</div>

<?php
$SiteURL = $this->Data("Provider.URL", 'Provider');
?>

<h3><?php echo $SiteURL; ?></h3>
<div class="Info"><?php 

   if ($this->Data('Provider') === FALSE) {
      echo '<div class="ErrorSetting">'.T("You need to configure ProxyConnect").'</div>';
   } else {
      
      $Required = array(
         'AuthenticateUrl'    => "The root URL of the remote application",
         'SignInUrl'          => "Sign In URL of the remote SSO application",
         'AuthenticationKey'  => "Required for foreign key association in the DB",
         'AssociationSecret'  => "Required for signing requests"
      );
      
      foreach ($Required as $AuthenticationPuzzleCheck => $PuzzleAbout) {
         $PuzzleCheck = $this->Data("Provider.{$AuthenticationPuzzleCheck}", FALSE);
         $PuzzleCheckStatus = $PuzzleCheck === FALSE ? 'Bad' : 'Ok';
         echo "<div class=\"GeneralSetting {$PuzzleCheckStatus}\">{$AuthenticationPuzzleCheck}<span>{$PuzzleAbout}</span></div>\n";
      }
   }
?></div>

<h3><?php echo T("Test ProxyConnect Link"); ?></h3>
<div class="Info">
   <p>Make sure you are logged in to <b><?php echo $SiteURL; ?></b>, then press 'Test!'.</p>
   <?php 
   echo $this->Form->Open(); 
   echo $this->Form->Errors(); 
   
   if ($this->Data('Attempt')) {
      
      $RawResponse = $this->Data('RawResponse');
      $ConnectResponse = $this->Data('ConnectResponse');
      echo '<div class="ResultsTitle">Remote Response</div>';
      if ($RawResponse != $ConnectResponse) {
         echo '<div class="ResultsSection Raw">';
         echo $RawResponse;
         echo '</div>';
      }
      echo '<div class="ResultsSection">';
      if (is_array($ConnectResponse)) {
         foreach ($ConnectResponse as $ConnectResponseField => $ConnectResponseValue) {
            echo "<div><b>{$ConnectResponseField}</b> {$ConnectResponseValue}</div>";
         }
      } else {
         print_r($ConnectResponse);
      }
      echo '</div>';
      
      if ($Connected = $this->Data('Connected')) {
         echo '<div class="ResultsTitle">Decoded Response</div>';
         echo '<div class="ResultsSection">';
         if (is_array($Connected)) {
            foreach ($Connected as $ConnectField => $ConnectValue) {
               echo "<div><b>{$ConnectField}</b> {$ConnectValue}</div>";
            }
         } else {
            print_r($Connected);
         }
         echo '</div>';
      }
      
      if ($NoParse = $this->Data('NoParse', FALSE)) {
         echo '<div class="ErrorTest">';
         echo T('ProxyConnect.NoParse', "Could not understand the <b>".$this->Data('ReadMode')."</b> response received 
            from the AuthenticateURL. Your application is not responding in a way
            that ProxyConnect understands!");
         echo '</div>';
      }
      
      if ($ConnectedAs = $this->Data('ConnectedAs')) {
         echo '<div class="SuccessTest">';
         echo Wrap("Test Successful", 'p');
         echo Wrap("You are logged in as <b>{$ConnectedAs}</b> at <b>{$SiteURL}</b>", 'div');
         echo '</div>';
      } else {
         if (!$NoParse) {
            echo '<div class="ErrorTest">';
            echo T('ProxyConnect.NoAuthenticate', "It doesn't seem like we were 
               able to retrieve a logged-in session from the AuthenticateURL you 
               specified. Please make sure you are logged in to your remote application 
               before performing this test.");
            echo '</div>';
            echo '<div class="ErrorTestFailed Login">Not logged in</div>';
         } else {
            echo '<div class="ErrorTestFailed">Test Failed</div>';
         }
      }
      
   }
   
   echo $this->Form->Close('Test!');
   ?>
</div>
<style type="text/css">
   .ErrorSetting {
      font-size: 24px;
      text-transform: uppercase;
      font-weight: bold;
      color: #a00000;
   }
   
   .GeneralSetting {
      padding-left: 40px;
      height: 48px;
      font-weight: bold;
   }
   .GeneralSetting.Ok {
      background: transparent url('/applications/dashboard/design/images/check.png') no-repeat 0px 8px;
   }
   .GeneralSetting.Bad {
      color: #a00000;
   }
   .GeneralSetting span {
      display: block;
      color: #8D8D8D;
      text-transform: lowercase;
      font-weight: normal;
      font-size: 12px;
   }
   
   .ResultsTitle {
      font-weight: bold;
      margin-bottom: 5px;
   }
   .ResultsSection {
      width: 600px;
      background: #E9E9E9;
      padding: 15px;
      white-space: pre;
      margin-bottom: 15px;
   }
   .ResultsSection.Raw {
      font-family: "Courier New", "Courier", "Terminal";
   }
   .ResultsSection div {
      color: #5A5A5A;
      margin: 4px 0px;
   }
   .ResultsSection div b {
      color: black;
   }
   
   .SuccessTest {
      padding-left: 40px;
      margin: 10px 0px 20px 0px;
      background: transparent url('/applications/dashboard/design/images/check.png') no-repeat 0px 8px;
   }
   .SuccessTest p {
      color: #8bb035;
      font-weight: bold;
      font-size: 24px;
      margin: 0px;
      text-transform: uppercase;
   }
   
   .ErrorTest {
      color: #a00000;
      width: 600px;
      margin: 10px 0px 20px 0px;
      background: #ffd8d8;
      padding: 15px;
   }
   
   .ErrorTestFailed {
      color: #a00000;
      margin: 10px 0px 20px 0px;
      font-weight: bold;
      font-size: 24px;
      text-transform: uppercase;
   }
   .ErrorTestFailed.Login {
      color: #515151;
   }
   
   .Buttons {
      margin-top: 10px;
   }
   .Buttons .Button {
      margin: 0px;
   }
</style>