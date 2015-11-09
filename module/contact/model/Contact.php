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
    //public $lista = 0;
    public $iscritto = MYSQL_DATETIME;
    public $active = true;
    public $lastedit = MYSQL_DATETIME;
    public $privacy = 0;
    public $privacy_url = "";
    public $type = array('html','text');
    public $hask = "";
    ##
    public static function count(){
        $sql = 'SELECT COUNT(id) AS totale FROM '.self::table();
        $res = schemadb::execute('row',$sql);
        return number_format($res['totale'],0,",",".");
    }
    ##
    public static function makeHask($contact){
        $c = Contact::load($contact);
        $t = time();
        $c->hask = substr(md5($c->email.$t.$c->iscritto),5,11);
        $c->store();
        return $c->hask;
        
    }
    ##
    public static function checkHask($hask){
        return self::query(
                array(
                    'hask' => $hask,
                ));
    }
    ##
    public static function deActive($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 0;
        $c->store();
    }
    ##
    public static function ActivePrivacy($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 1;
        $c->store();
    }
    ##
    public static function Active($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->active = 1;
        $c->store();
    }
    ##
    public static function deActivePrivacy($hask){
        $co = self::checkHask($hask);
        $c = $co[0];
        $c->lastedit = MYSQL_NOW();
        $c->privacy = 0;
        $c->store();
    }
    
}
Contact::schemadb_update();

