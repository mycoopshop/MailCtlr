<?php

require_once __BASE__.'/module/contact/grid/ListaGrid.php';
require_once __BASE__.'/module/contact/model/Lista.php';


class ListaController {
    
    ##
	public function indexAction() {		
		$app = App::getInstance();		
		$grid = new ListaGrid();
		$app->render(array(
			'title'		=> 'Liste',
			'createUrl' => __HOME__.'/lista/create',
			'grid'		=> $grid->html(),
		));
	}
    
    ##
	public function createAction() {		
		$app = App::getInstance();
        $item = new Lista();
        $item->creata = MYSQL_NOW();
        
		$app->render(array(
			'title'		=> 'Nuovo Lista',
			'closeUrl'	=> __HOME__.'/lista',
			'item'		=> $item,
		));
        
	}
    
    ##
	public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = Lista::load($id);
        $app->render(array(
			'title' => 'Dettaglio Lista',
			'modifyUrl' => __HOME__.'/lista/modify/id/'.$id,
			'item'	=> $item,			
		));
	}
    
    ##
	public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = Lista::load($id);
		$app->render(array(
			'title' => 'Modifica Lista',
			'item'	=> $item,			
		));
	}
    
    ##
	public function saveAction() {		
		$app = App::getInstance();		
		$item = Lista::build($_POST);
        $item->user_id = $app->user["id"];
		$item->store();
		$app->redirect(__HOME__.'/lista/');
	}
    
    ##
	public function gridAction() {
		$grid = new ListaGrid();
		echo json_encode($grid->json());
	}
    
    ##
    public function deleteAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		Lista::delete($id);		
		$app->redirect(__HOME__.'/lista/');
    }
        
    
    
}