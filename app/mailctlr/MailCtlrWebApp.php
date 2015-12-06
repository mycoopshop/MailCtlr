<?php
require_once __BASE__.'/model/app/PrivateWebApp.php';

class MailCtlrWebApp extends PrivateWebApp {
	
	public $acl = array(
		
        'public' => array(
			'*'                                 => false,
			'userrole.Login.*'                  => true,
            'contact.Remote.*'                  => true,
            'install.*'                         => true,
            
		),
		'user' => array(
			'*'                                 => true,   
            'devnote.*'                         => false,
            'config.*'                          => false,
		),
		'admin' => array(
			'*'                                 => true,
            'userrole.User.*'                   => false,
            'userrole.User.render'              => true,   
		),
        'superadmin' => array(
			'*'  => true,
		),
	);
	
}
date_default_timezone_set ('Europe/Rome');
ini_set('memory_limit', '2048M');
//ini_set('max_execution_time', 0);
