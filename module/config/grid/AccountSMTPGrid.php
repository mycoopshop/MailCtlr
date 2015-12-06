<?php
require_once __BASE__.'/model/grid/Grid.php';

class AccountSMTPGrid extends Grid {
    
	public function __construct() {		
		$this->id = 'AccountSMTPGrid';                       
		$this->source = AccountSMTP::table();                
		$this->endpoint = __HOME__.'/accountSMTP/grid';
        
		$this->columns = array(
			'id' =>array(
                    'visible'=>false,
            ),
            'code' => array(
                    'label'=>'Server Code'
            ),                        
            'created' => array(
                'label'=>'Creato il' 
            ),
            'name' => array(
                'label' => 'Nome',
            ),
            'max_mail' => array(
                'label'=>'Limite',
            ),
            'ever' => array(
                'label' => 'Ogni',
            ),
            'active' => array(
                'label' => 'Active',
            ),
            'command' => array(
                'label'=>'Command',
                'field' => 'id',
                'sortable'=>false,
                'html' =>
                    '<a href="'.__HOME__.'/accountSMTP/detail/id/{?}" class="btn btn-xs btn-success"> View</a> '.
                    '<a href="'.__HOME__.'/accountSMTP/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> Edit</a> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/accountSMTP/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Delete</button>',
			),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;', 			
		);
	}	
}