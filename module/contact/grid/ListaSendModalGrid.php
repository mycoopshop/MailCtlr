<?php

require_once __BASE__.'/model/grid/Grid.php';
require_once __BASE__.'/module/contact/model/Contact.php';
require_once __BASE__.'/module/contact/model/Lista.php';

class ListaSendModalGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'ListaSendModalGrid';

        $table = Iscrizioni::table();
        $table_l = Lista::table();
        $table_c = Contact::table();

        $this->source = '('
                        .'SELECT COUNT(c.id) AS num_iscritti , l.id, l.creata, l.nome  '
                        ."FROM $table_c AS c, $table_l AS l, $table AS i "
                        .'WHERE c.id = i.contatto_id AND i.lista_id = l.id '
                        .'GROUP BY l.id '
                        .') AS t';

        $this->endpoint = __HOME__.'/lista/modalGridJsonSend';

        $this->columns = [
            'id' => [
                'label' => 'ID',
            ],
            'nome' => [
                'label' => 'Descrizione',
            ],
            'creata' => [
                'label' => 'Creata il',
            ],
            'num_iscritti' => [
                'label' => 'Iscritti',
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
