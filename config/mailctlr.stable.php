<?php 

define('DAY',1);    // in giorni
define('WEEK',7);   // in giorni
define('MONTH',30); // in giorni
define('YEAR',365); // in giorni

##
return array(
	
	##
	'debug' => true,
	
	##
	'name' => 'MailCtlr',
	
	##
	'url' => 'http://www.ctlr.eu/MailCtlr',
	
	##
	'home' => 'http://www.ctlr.eu/MailCtlr',
	
	##
	'logo' => '/store/mailctlr/ctlr.png',
	
	##
	'db' => array(
		'host' => 'sql.ctlr.eu',
		'user' => 'ctlreu80978',
		'pass' => 'ctlr40882',
		'name' => 'ctlreu80978',
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
        //'payment',
	),
	
);

