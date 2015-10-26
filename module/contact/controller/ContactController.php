<?php

require_once __BASE__.'/module/contact/grid/ContactGrid.php';
require_once __BASE__.'/module/contact/model/Contact.php';


class ContactController {
    
    ##
	public function indexAction() {		
		$app = App::getInstance();		
		$grid = new ContactGrid();
		$app->render(array(
			'title'		=> 'Contatti',
			'createUrl' => __HOME__.'/contact/create',
			'grid'		=> $grid->html(),
		));
	}
    
    ##
	public function createAction() {		
		$app = App::getInstance();
        $item = new Contact();
        $item->iscritto = MYSQL_NOW();
        $item->lastedit = MYSQL_NOW();
        
		$app->render(array(
			'title'		=> 'Nuovo Contact',
			'closeUrl'	=> __HOME__.'/contact',
			'item'		=> $item,
		));
        
	}
    
    ##
	public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = Contact::load($id);
        $app->render(array(
			'title' => 'Dettaglio Contact',
			'modifyUrl' => __HOME__.'/contact/modify/id/'.$id,
			'item'	=> $item,			
		));
	}
    
    ##
	public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = Contact::load($id);
        $item->lastedit = MYSQL_NOW();
		$app->render(array(
			'title' => 'Modifica Contact',
			'item'	=> $item,			
		));
	}
    
    ##
	public function saveAction() {		
		$app = App::getInstance();		
		$item = Contact::build($_POST);
        $item->user_id = $app->user["id"];
		$item->store();
		$app->redirect(__HOME__.'/contact/');
	}
    
    ##
	public function gridAction() {
		$grid = new ContactGrid();
		echo json_encode($grid->json());
	}
    
    ##
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		Contact::delete($id);		
		$app->redirect(__HOME__.'/contact/');
    }
        
    
    
}