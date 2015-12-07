<?php

require_once __BASE__.'/model/grid/Grid.php';

class DettaglioGrid extends Grid
{
    public function __construct($lista)
    {
        //print($lista);die();
        $this->id = 'DettaglioGrid';
        $contact = Contact::table();
        $iscrizione = Iscrizioni::table();
        $this->source = '('
                        .'SELECT c.* '
                        ."FROM $contact AS c, $iscrizione AS i "
                        ."WHERE c.id = i.contatto_id AND i.lista_id = '".$lista."'"
                        .') AS t';

        $this->endpoint = __HOME__.'/iscrizioni/griddettaglio/id/'.$lista;

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
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/iscrizioni/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i>'._('Delete').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
