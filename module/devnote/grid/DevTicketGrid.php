<?php
require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/devnote/model/DevTicket.php';
require_once __BASE__.'/module/userrole/model/User.php'; 
require_once __BASE__.'/module/devnote/model/TicketComment.php';
class DevTicketGrid extends Grid { 	
	
	public function __construct() {		
	 
		$this->id = 'DevTicketGrid';		
		
		$table_t=  DevTicket::table();
		$table_u=  User::table();		
		
		$this->source = "("
			. "    SELECT c.*,u.nome AS nome_join, u.username AS user_join, a.username AS assegnato_join  "
			. "      FROM {$table_t} AS c "
			. " LEFT JOIN {$table_u} AS u "
			. "        ON c.user=u.id"
                        . " LEFT JOIN {$table_u} AS a "
			. "        ON c.assignedto=a.id"
			. ") AS t";
                        
		$this->endpoint = __HOME__.'/devnote/grid';
		
		$this->columns = array(
			'id'=>array(
				'label'=>'Segnalazione',
                                'width'=>100,
			),
			'user_join'=>array(
				'label'=>'Utente',
			),
			'category'=>array(
				'label'=>'Categoria',
			),
			'subject'=>array(
				'label'=>'Oggetto',
			),
			'stato'=>array(
				'label'=>'Stato',
			),
			'priority'=>array(
				'label'=>'Priorit&agrave;',
			),
			'assegnato_join'=>array(
				'label'=>'Assegnato a',
			),
			'command' => array(
				'label'=>'Comandi',
				'field' => 'id',
                                'sortable'=>false,
				'html' =>
					'<a href="'.__HOME__.'/devnote/detail/id/{?}" class="btn btn-xs btn-success"> Visualizza</a> '.
                                        '<a href="'.__HOME__.'/devnote/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> Modifica</a> '.
					'<button data-delete-url="'.__HOME__.'/devnote/delete/id/{?}" class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete"><i class="glyphicon glyphicon-trash"></i> Elimina</button>',
			),	
		);		
		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/devnote/detail/id/"+id;', 			
		);
	}	
}