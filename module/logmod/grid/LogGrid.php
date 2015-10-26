<?php
require_once __BASE__.'/model/grid/Grid.php';

class LogGrid extends Grid {
    
	public function __construct() {
        
		$this->id = 'LogGrid';                       
		$this->source = Log::table();                
		$this->endpoint = __HOME__.'/log/grid';
        
		$this->columns = array(
			'id' =>array(
                'visible'=>false,
            ),                       
            'action' => array(
                'label'=>'Azione' 
            ),
            'data' => array(
                'label' => 'Data',
            ),
            'user_id'=> array(
                'label'=>'ID Utente' 
            ),
            'user_org'=> array(
                'label'=>'Org Utente' 
            ),
            'user_country'=> array(
                'label'=>'Country' 
            ),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}