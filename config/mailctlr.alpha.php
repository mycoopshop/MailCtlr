<?php 

define('DAY',1);
define('WEEK',7);
define('MONTH',30);
define('YEAR',365);

##
return array(
	
	##
	'debug' => true,
	
	##
	'name' => 'MailCtlr',
	
    ##
    'version' => '0.1.2-alpha',
    
	##
	'url' => 'http://www.your-site.com/MailCtlr',
	
	##
	'home' => 'http://www.your-site.com/MailCtlr',
	
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
        'changelog',
	),
	
);

