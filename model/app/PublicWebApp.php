<?php
##
require_once(__BASE__.'/model/app/WebApp.php');

##
class PublicWebApp extends WebApp {

	##
	public $acl = array(
		'public' => array(
			'*' => true
		),
	);
	
	##
	public function __construct($file,$php_self,$request_uri) {
		parent::__construct($file,$php_self,$request_uri);		
	}
	
	
}