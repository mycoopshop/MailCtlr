<?php

require_once __BASE__.'/model/grid/Grid.php';

class ContactGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'ContactGrid';
        $this->source = Contact::table();
        $this->endpoint = __HOME__.'/contact/grid';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'azienda' => [
                'label' => _('Company'),
            ],
            'nome' => [
                'label' => _('Name'),
            ],
            'cognome' => [
                'label' => _('Surname'),
            ],
            'email' => [
                'label' => _('Email'),
            ],
            'active' => [
              'label' => _('State'),
            ],
            'type' => [
              'label' => _('Type'),
            ],
            'lastedit' => [
                'label' => _('Last edit'),
            ],
            'command' => [
                'label'    => _('Command'),
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<a href="'.__HOME__.'/contact/detail/id/{?}" class="btn btn-xs btn-success">'._('View').'</a> '.
                    '<a href="'.__HOME__.'/contact/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i>'._('Edit').'</a> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/contact/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i>'._('Delete').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
