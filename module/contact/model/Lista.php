<?php
require_once __BASE__.'/model/Storable.php';
##
class Lista extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    
    public $nome = "";
    public $descrizione = "";
    public $creata = MYSQL_DATETIME;
    
    public $privacy_url = "";
    
    ##
    public static function count(){
        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table();
        $res = schemadb::execute('row',$sql);
        return $res['totale'];
    }
        	
}

Lista::schemadb_update();
