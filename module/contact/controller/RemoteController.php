<?php

require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';

class RemoteController {
    
    ##
	public function activePrivacyAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::ActivePrivacy($hask);
        echo "{$hask} ok!";
        //$app->redirect(__HOME__.'/contact/');
	}
    
    ##
	public function deactivePrivacyAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::deActivePrivacy($hask);
        echo "{$hask} ok!";
        //$app->redirect(__HOME__.'/contact/');
	}
    
    ##
	public function deActiveAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::deActive($hask);
        echo "{$hask} ok!";
        //$app->redirect(__HOME__.'/contact/');
	}
    
    ##
	public function ActiveAction() {		
		$app = App::getInstance();		
		$hask = (string) $app->getUrlParam('hask');
        Contact::Active($hask);
        echo "{$hask} ok!";
        //$app->redirect(__HOME__.'/contact/');
	}
    
    
}