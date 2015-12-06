<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/userrole/model/User.php';

class UserGrid extends Grid
{
    ##

    public function __construct()
    {
        $this->id = 'UserGrid';
        $this->source = User::table();
        $this->endpoint = __HOME__.'/user/grid';
        $this->columns = [
            'id' => [
                    'visible' => false,
            ],
            'username' => [
                'label' => _('Username'),
            ],
            'cognome' => [
                'label' => _('Surname'),
            ],
            'nome' => [
                'label' => _('Name'),
            ],
            'role' => [
                'label' => _('Role'),
            ],
            'command' => [
                'label' => _('Command'),
                'field' => 'id',
                'html'  => '<a href="'.__HOME__.'/user/detail/id/{?}" class="btn btn-xs btn-success"> '._('View').'</a> '.
                    '<a href="'.__HOME__.'/user/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> '._('Edit').'</a> '.
                    '<a href="'.__HOME__.'/user/delete/id/{?}" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> '._('Delete').'</a>',
            ],
        ];

        /*
         * Onclik open item detail
         */
        $this->events = [
            'row.click' => 'window.location = "'.__HOME__.'/user/detail/id/"+id;',
        ];
    }
}
