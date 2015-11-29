<?php
require_once __BASE__.'/module/logmod/model/Log.php';
require_once __BASE__.'/module/userrole/model/User.php';

class LogmodModule {
	private $acl = array(
		
		##
		'superadmin'=>array(
			'menu-super'	=> true,
			'menu-admin'	=> true,
			'menu-user'     => true,
		),
		
		##
		'admin' => array(
			'menu-super'    => false,
			'menu-admin'	=> true,
			'menu-user'		=> true,
		),
		
		##
		'user' => array(
			'menu-super'	=> false,
			'menu-admin'    => false,
			'menu-user'		=> true,
		),
	);
    
    ##
    public function __construct() {	
        $app = App::getInstance();
        
        if ($app->testAcl('menu-super',$this->acl)) {
            $app->addMenu('navbar',array(
                'parent'	=> 'navbar-config',
                'label'		=> 'Log',
                'link'		=> __HOME__.'/log/'
            ));
        }
	}
    
   
    
} 
    