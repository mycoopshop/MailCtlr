<?php

require_once __BASE__.'/model/grid/Grid.php';

class EmailGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'EmailGrid';
        $this->source = Email::table();
        $this->endpoint = __HOME__.'/email/grid';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'oggetto' => [
                'label' => _('Subject'),
            ],
            'created' => [
                'label' => _('Created'),
            ],
            'execute' => [
                'label' => _('Execution'),
            ],

            'command' => [
                'label'    => _('Command'),
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<a href="'.__HOME__.'/email/detail/id/{?}" class="btn btn-xs btn-success">'._('View').'</a> '.
                    '<a href="'.__HOME__.'/email/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i>'._('Edit').'</a> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/email/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i>'._('Delete').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
