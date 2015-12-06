<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';

class ContactModalGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'ContactModalGrid';
        $this->source = Contact::table();
        $this->endpoint = __HOME__.'/contact/modalGridJson';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'azienda' => [
                'label' => 'Azienda',
            ],
            'cognome' => [
                'label' => 'Cognome',
            ],
            'email' => [
                'label' => 'Email',
            ],
            'command' => [
                'label'    => 'Command',
                'field'    => 'id',
                'width'    => '10%',
                'sortable' => false,
                'html'     => '<button data-select-id="{?}" class="btn btn-xs btn-primary" type="button">Seleziona</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
