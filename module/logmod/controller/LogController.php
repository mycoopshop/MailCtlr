<?php

require_once __BASE__.'/module/logmod/grid/LogGrid.php';
require_once __BASE__.'/module/logmod/model/Log.php';


class LogController {
    
    ##
	public function indexAction() {		
		$app = App::getInstance();		
		$grid = new LogGrid();
        
        $info_user = User::getInfoUser();
        
        $log = array(
            'action' => "Read Log", //azione fatta
            'data' => MYSQL_NOW(),
            'user_id' => 0,
            'user_ip' => $info_user->ip,
            'user_hostname' =>  $info_user->hostname,
            'user_city' => $info_user->city,
            'user_region' => $info_user->region,
            'user_country' => $info_user->country,
            'user_loc' => $info_user->loc,
            'user_org' => $info_user->org,
        );
        
        
        //Log::submit($log);
        
		$app->render(array(
			'title'		=> 'Log di Sisitema',
			'grid'		=> $grid->html(),
		));
	}
    
    ##
	public function gridAction() {
		$grid = new LogGrid();
		echo json_encode($grid->json());
	}
    
}