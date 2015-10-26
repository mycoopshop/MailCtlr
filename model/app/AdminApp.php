<?php
require_once(__BASE__.'/lib/liberty/App.php');
require_once(__BASE__.'/lib/curi/curi.php');

class AdminApp extends App {
	
	public $acl = array(
		'admin'	=> array(
			"*"						=> 1,		
		),
		'super_admin' => array(
			"*"						=> 1,		
		),
		'public' => array(
			"*"						=> 0, 			
			"userrole.Login.*"		=> 1,		
		),
	);
		
	public function __construct($params) {
		parent::__construct($params);
		$this->init();
	}
	
	public function main() {
		$this->load();
		$this->exec();
	}
	
	public function error($error) {
		
		$type = isset($error["type"]) ? $error["type"] : "undefined";
				
		if ($type == "ACCESS_DENIED") {
			if (in_array('Operatore',$this->user["role"])) {
				#$app->Redirect("logger/index");
			} else {				
				$this->Redirect("login/index",array(
					'redirect'=>curi_get_current())
				);				 
			}						
		}
		
		$this->render_theme(array(
			"view" => $error["message"],
		));		
	}
	
}
