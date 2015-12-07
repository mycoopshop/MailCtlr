<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';

class IscrizioniGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'IscrizoniGrid';

        $table = Iscrizioni::table();
        $table_l = Lista::table();
        $table_c = Contact::table();

        $this->source = '('
                        .'SELECT COUNT(c.id) AS num_iscritti , l.id, l.creata, l.nome  '
                        ."FROM $table_c AS c, $table_l AS l, $table AS i "
                        .'WHERE c.id = i.contatto_id AND i.lista_id = l.id '
                        .'GROUP BY l.id '
                        .') AS t';

        $this->endpoint = __HOME__.'/iscrizioni/grid';

        $this->columns = [
            'id' => [
                'label' => 'ID',
            ],
            'nome' => [
                'label' => _('Description'),
            ],
            'creata' => [
                'label' => _('Created'),
            ],
            'num_iscritti' => [
                'label' => _('Members'),
            ],

            'command' => [
                'label'    => _('Command'),
                'field'    => 'id',
                'sortable' => false,
                'html'     => '<a href="'.__HOME__.'/iscrizioni/dettaglio/id/{?}" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-th-list"></i>'._('Members').'</a> '.
                    '<a href="'.__HOME__.'/iscrizioni/formIscrizione/id/{?}" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-th-list"></i>'._('Collect subscriptions').'</a> ',
            ],

        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }
}
