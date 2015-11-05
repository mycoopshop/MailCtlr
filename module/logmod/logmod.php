<?php
require_once __BASE__.'/module/logmod/model/Log.php';
require_once __BASE__.'/module/userrole/model/User.php';

class LogmodModule {
	
    ##
    public function __construct() {	
        //$app = App::getInstance();
        //$userInfo = User::getInfoUser();
        
        $first = array(
            'action'    =>  'Activate Log Module',
            'data'      =>  MYSQL_NOW(),
            'ip'      => User::getUserIp(),
            'cod_utente'=> '',
        );
        
        //Log::submit($first);
        $app = App::getInstance();
        $app->addMenu('navbar',array(
			'parent'	=> 'navbar-config',
			'label'		=> 'Log',
			'link'		=> __HOME__.'/log/'
		));
	}
    
   
    
} 
    