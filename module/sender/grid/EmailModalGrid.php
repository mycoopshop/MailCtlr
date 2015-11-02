<?php
require_once __BASE__.'/model/grid/Grid.php';

class EmailModalGrid extends Grid {
    
	public function __construct() {
        
		$this->id = 'EmailGrid';                       
		$this->source = Email::table();                
		$this->endpoint = __HOME__.'/email/modalGridJson';
        
		$this->columns = array(
			'id' =>array(
                'visible'=>false,
            ),                     
            'oggetto' => array(
                'label'=>'Oggetto' 
            ),
            'created' => array(
                'label' => 'Creata',
            ),
            'execute' => array(
                'label' => 'Eseguita',
            ),
            
            'command' => array(
                'label'=>'Command',
                'field' => 'id',
                'sortable'=>false,
                'html' =>
                    '<button data-select-id="{?}" class="btn btn-xs btn-primary" type="button">Seleziona</button>',
            ), 
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}