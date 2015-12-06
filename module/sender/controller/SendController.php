<?php

require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';
require_once __BASE__.'/module/sender/model/Coda.php';
require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';
require_once __BASE__.'/module/sender/grid/SendGrid.php';
require_once __BASE__.'/module/sender/grid/ProcessateGrid.php';

class SendController{
    
    ##
    public function indexAction(){   
        $app = App::getInstance();		
		$grid = new SendGrid();
        $app->appendJs(__HOME__.'/module/sender/js/process.js');      
        
        $tot = Coda::attendSend() > AccountSMTP::getRemainMail() ? AccountSMTP::getRemainMail() : Coda::attendSend();
        $number =  $tot / 1;
        
		$app->render(array(
			'title'		=> _('Queue Send by Email'),
			'createUrl' => __HOME__.'/send/create',
            'realTime'  => __HOME__.'/send/live',
            'modal'     => '#processModal',
            'number'    => $number,
            'action'    => __HOME__.'/remote/process',
            'totale'    => $tot,
            'grid'		=> $grid->html(),
		));
    }
    
    ##
    public function processateAction(){   
        $app = App::getInstance();		
		$grid = new ProcessateGrid();
        
		$app->render(array(
			'title'		=> _('Email sent'),
			'createUrl' => __HOME__.'/remote/create',
            'grid'		=> $grid->html(),
		));
    }
    
    ##
    public function liveAction(){   
        $app = App::getInstance();
        
        $app->appendJs(__HOME__.'/module/sender/js/process.js');
        
        $tot = Coda::attendSend() > AccountSMTP::getRemainMail() ? AccountSMTP::getRemainMail() : Coda::attendSend();
        $number =  $tot / 1;
        
		$app->render(array(
			'title'		=> _('Sending email in real time'),
            'modal'     => '#processModal',
            'number'    => $number,
            'action'    => __HOME__.'/remote/process',
            'totale'    => $tot,
		));
        
    }
    
    ##
    public function createAction() {		
		$app = App::getInstance();				
		$app->render(array(
			'title'		=> _('New process'),
			'closeUrl'	=> __HOME__.'/send',
			'item'		=> new User(),
		));
    } 
    
    /*
     * Function don't used
     */
    ##
    public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = User::load($id);		
		$app->render(array(
			'title' => 'Dettaglio Utente',
			'modifyUrl' => __HOME__.'/send/modify/id/'.$id,
			'item'	=> $item,			
		));
    }
    
    ##
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		Coda::delete($id);		
		$app->redirect(__HOME__.'/send/');
    }
    
    /*
     * Function don't used 
     */
    ##
    public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = User::load($id);
		$item->password = "";		
		$app->render(array(
			'title' => 'Modifica Utente',
			'item'	=> $item,			
		));
	}
    
    ##
    public function saveAction() {		
		$app = App::getInstance();		
        Coda::addCode($_POST['lista'], $_POST["email"]);
		$app->redirect(__HOME__.'/send/');
    }
    
    ##
    public function gridAction() {
		$grid = new SendGrid();
		echo json_encode($grid->json());			
    }
    
    ##
    public function processategridAction() {
		$grid = new ProcessateGrid();
		echo json_encode($grid->json());			
    }
    
    ##
    public function renderAction() {
        $item = Coda::load($_POST['id']);		
		echo json_encode($item);
    }
    
    ##
    public function stateAction(){
        $app = App::getInstance();
		$app->render(); 			
    }
    
    ##
    public function dropCodaAction(){
        Coda::drop();
        $app = App::getInstance();
        $app->redirect(__HOME__);
    }
    
    ##
    public function noticePrivacyAction(){
       
    }
    
    
}

