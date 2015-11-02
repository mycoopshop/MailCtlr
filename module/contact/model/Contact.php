<?php
require_once __BASE__.'/model/Storable.php';
##
class Contact extends Storable {
	public $id = MYSQL_PRIMARY_KEY;

    public $nome = "";
    public $cognome = "";
    public $telefono = "";
    public $cellulare = "";
    
    public $azienda = "";
    public $indirizzo = "";
    public $piva = "";
    public $cap = "";
    public $citta = "";
    public $provincia = "";
    public $email = "";    
    
    public $lista = 0;
    
    public $iscritto = MYSQL_DATETIME;
    public $active = true;
    public $lastedit = MYSQL_DATETIME;

    public $privacy = 0;
    public $privacy_url = "";
    
    public $type = array('html','text');
    
    
    ##
    public static function count(){
        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table();
        $res = schemadb::execute('row',$sql);
        return number_format($res['totale'],0,",",".");
    }
}
Contact::schemadb_update();

