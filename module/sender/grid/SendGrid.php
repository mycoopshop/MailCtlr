<?php
require_once __BASE__.'/model/grid/Grid.php';

class SendGrid extends Grid {
    
	public function __construct() {
        
		$this->id = 'SendGrid';                       
		$this->source = Coda::table();                
		$this->endpoint = __HOME__.'/send/grid';
        
		$this->columns = array(
			'id' =>array(
                'visible'=>false,
            ),                     
            'contact_id' => array(
                'label'=>'ID Contatto' 
            ),                     
            'email_id' => array(
                'label'=>'ID Email' 
            ),
            'created' => array(
                'label'=>'Creato' 
            ),
            'execute' => array(
                'label' => 'Inviato',
            ),
            'processato' => array(
                'label' => 'Processato',
            ),
            'server_id' => array(
                'label' => 'Server',
            ),
            'command' => array(
                'label'=>'Command',
                'field' => 'id',
                'sortable'=>false,
                'html' =>
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/send/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Delete</button>',
			),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}