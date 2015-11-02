<?php
require_once __BASE__.'/model/Storable.php';
require_once __BASE__.'/module/contact/model/Contact.php';

##
class Iscrizioni extends Storable {
	public $id = MYSQL_PRIMARY_KEY;
    public $user_id = 0;
    public $creata = MYSQL_DATETIME;
    
    public $lista_id = 0;
    public $contatto_id = 0;
        
    public $active = 1;
    
    
    //da vedere con franco! :D :P
    ##
    public static function getList($lista=""){
        return Iscrizioni::query(
                array(
                    'lista_id'  => $lista,
                    'active'    => 1,
                )
        );
    }
    
}

Iscrizioni::schemadb_update();
