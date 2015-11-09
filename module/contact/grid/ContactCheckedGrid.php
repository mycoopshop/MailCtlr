<?php
require_once __BASE__.'/model/grid/Grid.php';

class ContactCheckedGrid extends Grid {
    
	public function __construct() {
        
		$this->id = 'ContactCheckedGrid'; 
        $c = Contact::table();
        
        
		$this->source = "("
                        . "SELECT *  "
                        . "FROM $c AS c "
                        . "WHERE c.verificato = 1 "
                        . ") AS t";
        
		$this->endpoint = __HOME__.'/contact/gridChecked';
        
		$this->columns = array(
			'id' =>array(
                'visible'=>false,
            ),                     
            'azienda' => array(
                'label'=>'Azienda' 
            ),                     
            'nome' => array(
                'label'=>'Nome' 
            ),
            'cognome' => array(
                'label'=>'Cognome' 
            ),
            'email' => array(
                'label' => 'Email',
            ),
            
            'active' => array(
              'label' => 'Stato'  ,
            ),
            'type' => array(
              'label' => 'Type'  ,
            ),
            'lastedit'=> array(
                'label'=>'Last Edit' 
            ),
            
            'command' => array(
                'label'=>'Command',
                'field' => 'id',
                'sortable'=>false,
                'html' =>
                    '<a href="'.__HOME__.'/contact/detail/id/{?}" class="btn btn-xs btn-success"> View</a> '.
                    '<a href="'.__HOME__.'/contact/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> Edit</a> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/contact/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Delete</button>',
			),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}