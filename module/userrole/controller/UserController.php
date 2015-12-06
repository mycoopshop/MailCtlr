<?php
require_once __BASE__.'/module/userrole/grid/UserGrid.php';
class UserController{
    
    ##
    public function indexAction(){   
        $app = App::getInstance();		
		$grid = new UserGrid();		
		$app->render(array(
			'title'		=> _('User list'),
			'createUrl' => __HOME__.'/user/create',
			'grid'		=> $grid->html(),
		)); 			
    }
    
    ##
    public function createAction() {		
		$app = App::getInstance();				
		$app->render(array(
			'title'		=> _('New user'),
			'closeUrl'	=> __HOME__.'/user',
			'item'		=> new User(),
		));
    }
    
    ##
    public function detailAction() {		
		$app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		$item = User::load($id);		
		$app->render(array(
			'title' => _('User detail'),
			'modifyUrl' => __HOME__.'/user/modify/id/'.$id,
			'item'	=> $item,			
		));
    }
    
    ##
    public function deleteAction(){
                $app = App::getInstance();		
		$id = (int) $app->getUrlParam('id');		
		User::delete($id);		
		$app->redirect(__HOME__.'/user/');
    }
    
    ##
    public function modifyAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = User::load($id);
		$item->password = "";		
		$app->render(array(
			'title' => _('Edit user'),
			'item'	=> $item,			
		));
	}
    
    ##
    public function saveAction() {		
		$app = App::getInstance();		
		if ($_POST['password']) {
			$_POST['password'] = md5($_POST['password']);
		} else {
			unset($_POST['password']);
		}
		$item = User::build($_POST);
		$item->store();		
		$app->redirect(__HOME__.'/user/');
    }
    
    ##
    public function gridAction() {
		$grid = new UserGrid();	
		echo json_encode($grid->json());			
    }

    ##
    public function renderAction() {
        $item = User::load($_POST['id']);		
		echo json_encode($item);
    }
  
}    
