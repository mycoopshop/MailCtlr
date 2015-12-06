<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/userrole/model/User.php';

class UserGrid extends Grid {
    
    ##    
	public function __construct() {		
		$this->id = 'UserGrid';
		$this->source = User::table();
		$this->endpoint = __HOME__.'/user/grid';
		$this->columns = array(
			'id' => array(
                    'visible'=>false,
            ),
			'username' => array(
				'label' => _('Username'),
			),
            'cognome' => array(
                'label' => _('Surname'),
            ),
            'nome'=>array(
                'label' => _('Name'),
            ),
            'role' => array(
                'label' => _('Role'),
            ),
            'command' => array(
                'label' => _('Command'),
				'field' => 'id',
				'html' => '<a href="'.__HOME__.'/user/detail/id/{?}" class="btn btn-xs btn-success"> '._('View').'</a> '.
                    '<a href="'.__HOME__.'/user/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> '._('Edit').'</a> '.
					'<a href="'.__HOME__.'/user/delete/id/{?}" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> '._('Delete').'</a>',
			),
		);
        
        /*
         * Onclik open item detail
         */
		$this->events = array(
			'row.click' => 'window.location = "'.__HOME__.'/user/detail/id/"+id;', 			
		);
        
	}	
}