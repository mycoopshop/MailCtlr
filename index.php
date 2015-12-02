<?php 
 define('__NAME__','mailctlr');
 define('__MODE__','install');
 require_once 'bootstrap.php';
 require_once __BASE__.'/model/app/PublicWebApp.php';
 $app = new PublicWebApp( __FILE__ , $_SERVER['PHP_SELF'], $_SERVER['REQUEST_URI'] ); 
 $app->run(); 