<?php
require_once __BASE__.'/model/grid/Grid.php';

class SendGrid extends Grid {
    
	public function __construct() {
        $this->id = 'SendGrid';
        
        $coda = Coda::table();
        $contatto = Contact::table();
        $email = Email::table();
        
		$this->source = "("
                        . " SELECT c.*, em.oggetto AS oggetto, co.email AS email, CONCAT(co.azienda,' ',co.nome,' ',co.cognome) AS fullname  "
                        . " FROM $coda AS c "
                        . " LEFT JOIN $contatto AS co "
                        . " ON c.contact_id = co.id "
                        . " LEFT JOIN $email AS em "
                        . " ON c.email_id = em.id "
                        . " WHERE c.processato = '0' "
                        . " ) AS t";
                
		$this->endpoint = __HOME__.'/send/grid';
        
		$this->columns = array(
			'id' =>array(
                'visible'=>false,
            ),                     
            'fullname' => array(
                'label'=>'Nome', 
            ),                     
            'email' => array(
                'label'=>'Email' 
            ),
            'oggetto' => array(
                'label' => 'Oggetto',
            ),
            'created' => array(
                'label'=>'Creato' 
            ),
            /*'command' => array(
                'label'=>'Command',
                'field' => 'id',
                'sortable'=>false,
                'html' =>
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/send/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Delete</button>',
			),*/
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}