<?php

require_once __BASE__.'/model/Storable.php';

##
class Changelog extends Storable
{
    public $id = MYSQL_PRIMARY_KEY;
    public $data = MYSQL_DATETIME;

    public $title = '';
    public $version_numb = '';
    public $version_name = '';
    public $version_type = '';
    public $text = MYSQL_TEXT;

    ##

    public static function current()
    {
        $sql = 'SELECT * FROM '.self::table().' ORDER BY id DESC LIMIT 1 ';
        $row = schemadb::execute('row', $sql);
        $a = self::build($row);

        return $a;
    }
}
Changelog::schemadb_update();
