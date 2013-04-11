<?php 

/*

DO NOT USE THIS ERROR PAGE FOR 50X ERRORS, AS THEY MAY BE GENERATED DUE TO BAD PHP CONFIGURATION. 

USE THIS FILE (error.php?code=501) TO RENDER THE SOURCE AND PUT IT IN A HTML FILE, 50X.html

 */
$code = $_GET['code'] ?: null;
require(realpath(dirname(__FILE__) . '/../..').'/library/Masthead.php' );
$masthead = new PracticalPlants_Masthead();
?><!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>502 - Server Error</title>
    <?php $masthead->headTags(); ?>
    <link rel="stylesheet" type="text/css" href="/errors/errors.css">
  </head>
  <body class="">
    <?php //echo $this->masthead(); 
    
    $masthead->output();
    
    ?>
    <nav class="masthead-submenu" id="submenu">
    </nav>
    <div id="main">
      <img src="/errors/logo.png" />
      <?php switch($code){ 
          case 403: ?>
        <h1>Bad Request</h1>
        <h3 class="code">400</h3>
        <p>The server did not understand the request your browser sent. This may be due to a browser error, or a server problem.</p>
        <p>If you think this is an error on our part email <a href="mailto:help@practicalplants.org">help@practicalplants.org</a>.</p>
    <?php break;
          case 403: ?>
        <h1>Access Forbidden</h1>
        <h3 class="code">403</h3>
        <p>The content you attempted to access is not publicly accessible.</p>
        <p>If you think this is an error on our part email <a href="mailto:help@practicalplants.org">help@practicalplants.org</a>.</p>
    <?php break;
          case 404:?>
        <h1>File Not Found</h1>
        <h3 class="code">404</h3>
        <p>We can't find the file you're looking for.</p>
        <p>If you think this is an error on our part email <a href="mailto:help@practicalplants.org">help@practicalplants.org</a>.</p>
    <?php break;
          case 501:
          case 502:
          case '50X' ?>
        <h1>Server Error!</h1>
        <h3 class="code"><?php echo $code ?></h3>
        <p>It looks like the server is having a bit of a problem. Try refreshing your browser.</p>
        <p>If things aren't back the way they should be soon, you can email <a href="mailto:help@practicalplants.org">help@practicalplants.org</a> for advice</p>
    <?php break;
          default: ?>
        <h1>Unknown Error!</h1>
        <p>It looks like the server is having a bit of a problem. Try refreshing your browser and see if it works yet</p>
        <p>If things aren't back the way they should be soon, you can email <a href="mailto:help@practicalplants.org">help@practicalplants.org</a> for advice</p>
    <?php } ?>
    </div>
    <?php $masthead->footer(); ?>
    <?php include(realpath(APPLICATION_PATH . '/../../library').'/google-analytics.html'); ?>
  </body>
</html>