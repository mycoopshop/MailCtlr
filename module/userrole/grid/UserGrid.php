<?php
require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/userrole/model/User.php';
class UserGrid extends Grid {    
	public function __construct() {		
		$this->id = 'UserGrid';
		$this->source = User::table();
		$this->endpoint = __HOME__.'/user/grid';
		$this->columns = array(
			'id' =>array(
                                'visible'=>false,
                        ),
			'username' => array(
				'label' => 'Nome Utente'
			),
                        'cognome'=>array(
                                'label'=>'Cognome',
                        ),
                        'nome'=>array(
                                'label'=>'Nome',
                        ),
                        'role' => array(
                                'label'=>'Ruolo',
                        ),
                        'command' => array(
                                'label'=>'Comandi',
				'field' => 'id',
				'html' =>
					'<a href="'.__HOME__.'/user/detail/id/{?}" class="btn btn-xs btn-success"> Visualizza</a> '.
                                        '<a href="'.__HOME__.'/user/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> Modifica</a> '.
					'<a href="'.__HOME__.'/user/delete/id/{?}" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Elimina</a>',
			),
		);		
		$this->events = array(
			//'row.click' => 'window.location = "'.__HOME__.'/user/detail/id/"+id;', 			
		);
	}	
}