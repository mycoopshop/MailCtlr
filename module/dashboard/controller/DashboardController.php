<?php
require_once __BASE__.'/module/config/model/AccountSMTP.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';
##
class DashboardController {
	
	##
	public function indexAction() {
		
		##
		$app = App::getInstance();
		
        $max_mail=AccountSMTP::getMaxMail();
        $total = AccountSMTP::getSenderMail();
        $remain = AccountSMTP::getRemainMail();
        $liste = Lista::count();
        $contatti = Contact::count();
        
		##
		$app->render(array(
			'max_mail'		=> $max_mail,
			'total'         => $total,
            'remain'        => $remain,
            'liste'         => $liste,
            'contatti'      => $contatti,
            )
        );
	}
    
    ##
    public function testAction(){
           $app=App::getInstance();
           $app->render();
    }
	
    ##
    public function licenseAction(){
           $app=App::getInstance();
           $app->render();
    }
}