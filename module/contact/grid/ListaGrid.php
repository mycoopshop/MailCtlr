<?php

require_once __BASE__.'/model/grid/Grid.php';

class ListaGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'ListaGrid';
        $this->source = Lista::table();
        $this->endpoint = __HOME__.'/lista/grid';

        $this->columns = [
            'id' => [
                'label' => 'ID',
            ],
            'nome' => [
                'label' => 'Nome',
            ],
            'descrizione' => [
                'label' => 'Descrizione',
            ],
            'creata' => [
                'label' => 'Creata il',
            ],
            //Aggiungere numero di utenti che ne fanno parte!
            'command' => [
                'label'    => 'Command',
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<a href="'.__HOME__.'/lista/detail/id/{?}" class="btn btn-xs btn-success"> View</a> '.
                    '<a href="'.__HOME__.'/lista/modify/id/{?}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-pencil"></i> Edit</a> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/lista/delete/id/{?}"><i class="glyphicon glyphicon-trash"></i> Delete</button> '.
                    '<button class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#modal-delete" data-delete-url="'.__HOME__.'/lista/deleteAll/id/{?}"><i class="glyphicon glyphicon-trash"></i> Remove All</button> ',
            ],

        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
