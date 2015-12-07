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
                'html'     => '<button data-select-id="{?}" class="btn btn-xs btn-primary" type="button">'._('Select').'</button>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
