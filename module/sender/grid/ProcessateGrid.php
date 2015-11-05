<?php
require_once __BASE__.'/model/grid/Grid.php';

class ProcessateGrid extends Grid {
    
	public function __construct() {
        $this->id = 'ProcessateGrid';
        
        $coda = Coda::table();
        $contatto = Contact::table();
        //$server = AccountSMTP::table();
        
        
		$this->source = "("
                        . " SELECT c.*, co.email AS email, CONCAT(co.azienda,' ',co.nome,' ',co.cognome) AS fullname  "
                        . " FROM $coda AS c "
                        . " LEFT JOIN $contatto AS co "
                        . " ON c.contact_id = co.id "
                        . " WHERE c.processato = '1' "
                        . " ) AS t";
                
		$this->endpoint = __HOME__.'/send/processategrid';
        
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
            'execute' => array(
                'label'=>'Inviata' 
            ),
            'note' => array(
                'label' => 'Note',
            ),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}