<?php 

define('DAY',1);    // in giorni
define('WEEK',7);   // in giorni
define('MONTH',30); // in giorni
define('YEAR',365); // in giorni

##
return array(
	
	##
	'debug' => false,
	
	##
	'name' => 'MailCtlr',
	
    ##
    'version' => '0.2-dev',
    
	##
	'url' => 'http://www.ctlr.eu/MailCtlr',
	
	##
	'home' => 'http://www.ctlr.eu/MailCtlr',
	
	##
	'logo' => '/store/mailctlr/ctlr.png',
	
	##
	'db' => array(
		'host' => 'localhost',
		'user' => 'db_mailctlr',
		'pass' => 'mailctlr',
		'name' => 'mailctlr',
		'pref' => 'mc_',
	),
	##
	'default' => array(
		'theme'		 => 'standard',
		'controller' => 'Dashboard',
	),
	
	##
	'modules' => array(
		'dashboard',
        'contact',
        'sender',
		'config',
		'userrole',  
		'devnote',
        'logmod',
        'changelog',
        //'payment',
	),
	
);

