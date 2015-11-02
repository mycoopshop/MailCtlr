<?php
require_once __BASE__.'/model/Storable.php';
##
class Lista extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    
    public $nome = "";
    public $descrizione = "";
    public $creata = MYSQL_DATETIME;
               
    ##
    public static function count(){
        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table();
        $res = schemadb::execute('row',$sql);
        return number_format($res['totale'],0,",",".");
    }
        	
}

Lista::schemadb_update();
