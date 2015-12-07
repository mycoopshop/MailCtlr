<?php

require_once __BASE__.'/model/grid/Grid.php';

class AccountSMTPGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'AccountSMTPGrid';
        $this->source = AccountSMTP::table();
        $this->endpoint = __HOME__.'/accountSMTP/grid';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'code' => [
                'label' => _('Server Code'),
            ],
            'created' => [
                'label' => _('Created'),
            ],
            'name' => [
                'label' => _('Name'),
            ],
            'max_mail' => [
                'label' => _('Limit'),
            ],
            'ever' => [
                'label' => _('Ever'),
            ],
            'active' => [
                'label' => _('Active'),
            ],
            'command' => [
                'label'    => _('Command'),
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<a href="'.__HOME__.'/accountSMTP/detail/id/{?}" class="btn btn-xs btn-success">'._('View').'</a> '.
                            '<a href="'.__HOME__.'/accountSMTP/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> '._('Edit').'</a> '.
                            '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/accountSMTP/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> '._('Delete').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
