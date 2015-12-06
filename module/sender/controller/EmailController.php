<?php

require_once __BASE__.'/module/sender/model/Email.php';
require_once __BASE__.'/module/sender/grid/EmailGrid.php';
require_once __BASE__.'/module/sender/grid/EmailModalGrid.php';
require_once __BASE__.'/module/sender/lib/PHPMailer/PHPMailerAutoload.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';


class EmailController{
    
    ##
    public function indexAction(){   
        $app = App::getInstance();		
		$grid = new EmailGrid();		
		$app->render(array(
			'title'		=> 'Lista Email',
			'createUrl' => __HOME__.'/email/create',
			'grid'		=> $grid->html(),
		)); 			
    }
    
    ##
    public function createAction() {		
		$app = App::getInstance();
        $item = new Email();
        $item->messaggio_html = "";
        $item->messaggio_text = "";
        $item->created = MYSQL_NOW();
		$app->render(array(
			'title'		=> 'Nuova Email',
			'closeUrl'	=> __HOME__.'/email',
			'item'		=> $item,
		));
    }
    
    ##
    public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = Email::load($id);		
		$app->render(array(
			'title' => 'Dettaglio Email',
			'modifyUrl' => __HOME__.'/email/modify/id/'.$id,
			'item'	=> $item,			
		));
    }
    
    ##
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		Email::delete($id);		
		$app->redirect(__HOME__.'/email/');
    }
    
    ##
    public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = Email::load($id);
        $item->created = MYSQL_NOW();
		$app->render(array(
			'title' => 'Modifica Email',
			'item'	=> $item,			
		));
	}
    
    ##
    public function saveAction() {
        $app = App::getInstance();        
		$item = Email::build($_POST);
        $item->user_id = $app->user["id"];        
        $item->lastedit = MYSQL_NOW();
		$item->store();
		$app->redirect(__HOME__.'/email/');
    }
    
    ##
    public function gridAction() {		
		##
		$grid = new EmailGrid();		
		##
		echo json_encode($grid->json());			
    }

    ##
    public function renderAction() {		
        $item = Email::load($_POST['id']);		
		echo json_encode($item);
    }
    
    ##
    public function modalSearchAction() {
		$grid	= new EmailModalGrid();		
		echo $grid->html();		
	}
	
    ##
	public function modalGridJsonAction() {
		$grid	= new EmailModalGrid();		
		echo json_encode($grid->json());		
	}	
   
    
}    
