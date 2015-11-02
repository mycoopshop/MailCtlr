<?php
define('__NAME__','mailctlr');
define('__MODE__','alpha');

require_once 'bootstrap.php';

require_once __BASE__.'/app/mailctlr/MailCtlrWebApp.php';

$app = new MailCtlrWebApp(__FILE__,$_SERVER['PHP_SELF'],$_SERVER['REQUEST_URI']);

$app->run(); 

