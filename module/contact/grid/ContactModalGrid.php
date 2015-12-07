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
                'label' => _('Company'),
            ],
            'cognome' => [
                'label' => _('Surname'),
            ],
            'email' => [
                'label' => _('Email'),
            ],
            'command' => [
                'label'    => _('Command'),
                'field'    => 'id',
                'width'    => '10%',
                'sortable' => false,
                'html'     => '<button data-select-id="{?}" class="btn btn-xs btn-primary" type="button">'._('Select').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
