<?php

require_once __BASE__.'/model/grid/Grid.php';

class EmailModalGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'EmailGrid';
        $this->source = Email::table();
        $this->endpoint = __HOME__.'/email/modalGridJson';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'oggetto' => [
                'label' => 'Oggetto',
            ],
            'created' => [
                'label' => 'Creata',
            ],
            'execute' => [
                'label' => 'Eseguita',
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
