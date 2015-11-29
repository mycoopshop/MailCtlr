<?php
require_once __BASE__.'/model/grid/Grid.php';

class OptionsGrid extends Grid {
    
	public function __construct() {		
		$this->id = 'OptionsGrid';                       
		$this->source = Options::table();                
		$this->endpoint = __HOME__.'/options/grid';
        
		$this->columns = array(
			'id' =>array(
                    'visible'=>false,
            ),
            'name' => array(
                    'label'=>'Nome'
            ),                        
            'descrizione' => array(
                'label'=>'Descrizione' 
            ),
            'value' => array(
                'label' => 'Value',
            )
		);
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}