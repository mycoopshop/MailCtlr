<?php
require_once __BASE__.'/model/grid/Grid.php';

class DettaglioGrid extends Grid {
    
	public function __construct($lista) {
        //print($lista);die();
		$this->id = 'DettaglioGrid';
        $contact = Contact::table();
        $iscrizione = Iscrizioni::table();
		$this->source = "("
                        . "SELECT c.* "
                        . "FROM $contact AS c, $iscrizione AS i "
                        . "WHERE c.id = i.contatto_id AND i.lista_id = '".$lista."'"
                        . ") AS t";
        
		$this->endpoint = __HOME__.'/iscrizioni/griddettaglio/id/'.$lista;
        
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
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/iscrizioni/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Remove</button>',
			),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}