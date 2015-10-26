<?php

require_once __BASE__.'/module/config/grid/AccountSMTPGrid.php';
require_once __BASE__.'/module/config/model/AccountSMTP.php';


class AccountSMTPController {
    
    ##
	public function indexAction() {		
		$app = App::getInstance();		
		$grid = new AccountSMTPGrid();
		$app->render(array(
			'title'		=> 'Server SMTP',
			'createUrl' => __HOME__.'/accountSMTP/create',
			'grid'		=> $grid->html(),
		));
	}
    
    ##
	public function createAction() {		
		$app = App::getInstance();
        $item = new AccountSMTP();
        $item->created = MYSQL_NOW();
        
		$app->render(array(
			'title'		=> 'Nuovo Server SMTP',
			'closeUrl'	=> __HOME__.'/accountSMTP',
			'item'		=> $item,
		));
        
	}
    
    ##
	public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = AccountSMTP::load($id);
        $app->render(array(
			'title' => 'Dettaglio Server SMTP',
			'modifyUrl' => __HOME__.'/accountSMTP/modify/id/'.$id,
			'item'	=> $item,			
		));
	}
    
    ##
	public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = AccountSMTP::load($id);
		$app->render(array(
			'title' => 'Modifica Server SMTP',
			'item'	=> $item,			
		));
	}
    
    ##
	public function saveAction() {		
		$app = App::getInstance();		
		$item = AccountSMTP::build($_POST);
        $item->last_edit = MYSQL_NOW();
		$item->store();		
		$app->redirect(__HOME__.'/accountSMTP/');
	}
    
    ##
	public function gridAction() {
		$grid = new AccountSMTPGrid();
		echo json_encode($grid->json());
	}
    
    ##
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		AccountSMTP::delete($id);		
		$app->redirect(__HOME__.'/accountSMTP/');
    }
        
    
    
}