<?php

require_once __BASE__.'/model/Storable.php';
require_once __BASE__.'/module/contact/model/Contact.php';

##
class Iscrizioni extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    public $creata = MYSQL_DATETIME;

    public $lista_id = 0;
    public $contatto_id = 0;

    public $active = 1;

    ##

    public static function getList($lista = '')
    {
        return self::query(
            [
                'lista_id'  => $lista,
                'active'    => 1,
            ]
        );
    }

    /*
     * verify duplicate check!
    */
    ##

    public static function duplicate()
    {
        $sql = ' SELECT tb.id, COUNT(tb.contatto_id) as tot '
                  .' FROM (SELECT tb.* FROM '.self::table().' AS tb ORDER BY id DESC ) as tb '
                  .' GROUP BY contatto_id '
                  .' HAVING tot > 1 ';

        return schemadb::execute('results', $sql);
    }
}
Iscrizioni::schemadb_update();
