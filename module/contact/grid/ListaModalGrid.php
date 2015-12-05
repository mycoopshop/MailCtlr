<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';

class ListaModalGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'ListaModalGrid';

        $this->source = Lista::table();

        $this->endpoint = __HOME__.'/lista/modalGridJson';

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

            'command' => [
                'label'    => 'Command',
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<button data-select-id="{?}" class="btn btn-xs btn-primary" type="button">Seleziona</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
