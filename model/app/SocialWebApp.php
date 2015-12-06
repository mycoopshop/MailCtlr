<?php

require_once(__BASE__.'/model/app/WebApp.php');

class SocialWebApp extends WebApp {
	
	public $acl = array(
		'public' => array(
			'*' => false
		),
	);
	
	public function __construct($params) {
		parent::__construct($params);		
	}
	
}