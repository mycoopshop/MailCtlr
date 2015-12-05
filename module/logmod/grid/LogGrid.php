<?php

require_once __BASE__.'/model/grid/Grid.php';

class LogGrid extends Grid
{
    public function __construct()
    {
        $this->id = 'LogGrid';
        $table_l = Log::table();
        $table_u = User::table();

        $this->source = '('
                        .' SELECT l.*, u.username '
                        ." FROM $table_l AS l "
                        ." LEFT JOIN $table_u AS u "
                        .' ON l.user_id = u.id '
                        //. " WHERE l.user_id= '{$cliente}' "
                        .' ORDER BY l.data DESC '
                        .' ) AS t';

        $this->endpoint = __HOME__.'/log/grid';

        $this->columns = [
            'id' => [
                'visible' => false,
            ],
            'data' => [
                'label'    => 'Data',
                'width'    => '17%',
                'function' => 'LogGrid::formatta_data',
            ],
            'action' => [
                'label' => 'Azione',
            ],
            'username' => [
                'label' => 'Utente',
                'width' => '10%',
            ],
            'command' => [
                'label'     => 'Comandi',
                'field'     => 'id',
                'sortable'  => false,
                'width'     => '10%',
                'html'      => '<a href="'.__HOME__.'/log/detail/id/{?}" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-asterisk"></i> Dettaglio</a>',
            ],
        ];
        $this->events = [
            //'row.click' => 'window.location = "'.__HOME__.'/agenti/detail/id/"+id;',
        ];
    }

    ##

    public static function formatta_data($data)
    {
        return date('H:i:s d-m-Y', strtotime($data));
    }
}
