<?php   
require_once __BASE__.'/module/devnote/grid/DevTicketGrid.php';
##redmine
require_once __BASE__.'/lib/redmine/autoload.php';

class DevnoteController {
	public function ticketAction() {
        $app = App::getInstance();

        $grid=new DevTicketGrid();
		$app->render(array(
			'title' => 'Sviluppo &gt; Segnalazioni',
			'createUrl' => __HOME__.'/devnote/create/',
            'grid'		=> $grid->html(),
		));		
	}
	public function createAction() {
		$app = App::getInstance();
        $item=new DevTicket();
        $item->user=$app->user['id'];
		$app->render(array(
			'title' => 'Sviluppo &gt; Segnalazioni &gt; Nuova Segnalazione',
			'closeUrl' => __HOME__.'/devnote/ticket',
            'item'		=> $item,
		));
	}        
	public function gridAction() {
		$grid = new DevTicketGrid();
		echo json_encode($grid->json());
	}	
	public function detailAction() {
		$app = App::getInstance();
		$id = (int) $app->getUrlParam('id');
		$item = DevTicket::load($id);
        //$devticket=$id;
        //$comments=  TicketComment::load($devticket);//carica tutti i commenti con devticket con id?
		$app->render(array(
			'title' => 'Dettagli Ordine',
			'modifyUrl' => __HOME__.'/devnote/modify/id/'.$id,
			'item'	=>  $item,
            // 'commets'=> $comments,
           //  'comment' => new TicketComment(),
		));	 	
	}
	public function saveAction() {		
		$app = App::getInstance();
		$client = new Redmine\Client('http://redmine.lrdev.net/redmine', '460433207b3808ec35adaf53f3ca744273614551');
                
        $client->api('issue')->create(array(
            'project_id'    => 'mailctlr',
            'subject'       => $_POST['subject'],
            'description'   => $_POST['description'],
        ));

        /*$client->api('issue')->all([
            'limit' => 1000
        ]);*/

        DevTicket::build($_POST)->store();
		$app->redirect(__HOME__.'/devnote/ticket');
	}        
    public function comment_saveAction(){
        $app=App::getInstance();
        TicketComment::build($_POST)->store;
        $app->redirect($_SERVER['HTTP_REFERER']);//pagina precedente?
    }
    public function deleteAction(){
        $app = App::getInstance();		
        $id = (int) $app->getUrlParam('id');		
        DevTicket::delete($id);		
        $app->redirect(__HOME__.'/devnote/ticket');
    }

        
}