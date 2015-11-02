<?php

require_once __BASE__.'/module/contact/grid/ListaGrid.php';
require_once __BASE__.'/module/contact/grid/ListaModalGrid.php';
require_once __BASE__.'/module/contact/model/Lista.php';
require_once __BASE__.'/module/contact/model/Iscrizioni.php';
require_once __BASE__.'/module/contact/model/Contact.php';

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
    
    ##
    public function deleteAllAction(){
        $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$reply = "START<br />";
        $lista = Iscrizioni::query(array(
            'lista_id' => $id
        ));
        foreach ($lista as $contact) {
            Contact::delete($contact->contatto_id);
            $reply .= "contatto {$contact->contatto_id} eliminato - ";
            Iscrizioni::delete($contact->id);
            $reply .= "iscrizione {$contact->id} eliminata - ";
            $reply .= "<br />";
        }
        
        Lista::delete($id);	
        $reply .= "lista {$id} eliminata!<br />";
        $reply .= "END";
		$app->redirect(__HOME__.'/lista/');
    }
    
    ##
    public function modalSearchAction() {
		$grid	= new ListaModalGrid();		
		echo $grid->html();		
	}
	
    ##
	public function modalGridJsonAction() {
		$grid	= new ListaModalGrid();		
		echo json_encode($grid->json());		
	}
	
    ##
	public function renderAction() {		
		$item = Lista::load($_POST['id']);
		echo json_encode($item);
	}
    
}