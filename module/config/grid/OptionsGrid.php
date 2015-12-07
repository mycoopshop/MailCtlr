<?php

require_once __BASE__.'/model/grid/Grid.php';

class OptionsGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'OptionsGrid';
        $this->source = Options::table();
        $this->endpoint = __HOME__.'/options/grid';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'name' => [
                'label' => _('Name'),
            ],
            'descrizione' => [
                'label' => _('Description'),
            ],
            'value' => [
                'label' => _('Value'),
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
